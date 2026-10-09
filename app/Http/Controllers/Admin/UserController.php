<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Controller UserController (Admin)
 * Mengelola daftar pengguna, status sesi login real-time, keanggotaan VIP Premium, dan hak akses
 */
class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna beserta kartu metrik ringkasan
     */
    public function index(Request $request): View
    {
        $hasSessions = Schema::hasTable('sessions');
        $fifteenMinutesAgo = Carbon::now()->subMinutes(15)->timestamp;

        // Ambil ID pengguna yang aktif dalam 15 menit terakhir
        $activeUserIds = [];
        $sessionsByUser = collect();

        if ($hasSessions) {
            $rawSessions = DB::table('sessions')
                ->whereNotNull('user_id')
                ->orderBy('last_activity', 'desc')
                ->get();

            $sessionsByUser = $rawSessions->groupBy('user_id');

            $activeUserIds = DB::table('sessions')
                ->whereNotNull('user_id')
                ->where('last_activity', '>=', $fifteenMinutesAgo)
                ->pluck('user_id')
                ->unique()
                ->toArray();
        }

        // 1. Metrik Ringkasan (Stat Cards)
        $totalUsers        = User::count();
        $totalCustomers    = User::where('role', 'customer')->count();
        $totalAdmins       = User::where('role', 'admin')->count();
        $onlineUsersCount  = count($activeUserIds);
        $premiumUsersCount = User::where('is_premium', true)
            ->where(function ($q) {
                $q->whereNull('premium_until')->orWhere('premium_until', '>', now());
            })
            ->count();
        $googleUsersCount  = User::whereNotNull('google_id')->count();
        $emailUsersCount   = User::whereNull('google_id')->count();

        $stats = [
            'total_users'       => $totalUsers,
            'total_customers'   => $totalCustomers,
            'total_admins'      => $totalAdmins,
            'online_users'      => $onlineUsersCount,
            'premium_users'     => $premiumUsersCount,
            'google_users'      => $googleUsersCount,
            'email_users'       => $emailUsersCount,
        ];

        // 2. Query Pengguna dengan Filter & Pencarian
        $query = User::withCount(['orders', 'reviews', 'shippingAddresses'])
            ->withSum([
                'orders as total_spend' => function ($q) {
                    $q->whereIn('status', ['paid', 'in_production', 'shipped', 'completed']);
                }
            ], 'grand_total');

        // Pencarian Keyword
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter Peran (Role)
        if ($request->filled('role') && in_array($request->role, ['admin', 'customer'])) {
            $query->where('role', $request->role);
        }

        // Filter Status Keanggotaan & Online
        if ($request->filled('status')) {
            if ($request->status === 'online') {
                $query->whereIn('id', $activeUserIds);
            } elseif ($request->status === 'premium') {
                $query->where('is_premium', true)
                      ->where(function ($q) {
                          $q->whereNull('premium_until')->orWhere('premium_until', '>', now());
                      });
            } elseif ($request->status === 'regular') {
                $query->where(function ($q) {
                    $q->where('is_premium', false)
                      ->orWhere(function ($sub) {
                          $sub->whereNotNull('premium_until')->where('premium_until', '<=', now());
                      });
                });
            }
        }

        // Filter Metode Login (Provider)
        if ($request->filled('provider')) {
            if ($request->provider === 'google') {
                $query->whereNotNull('google_id');
            } elseif ($request->provider === 'email') {
                $query->whereNull('google_id');
            }
        }

        // Sorting
        $sortBy = $request->input('sort', 'latest');
        match ($sortBy) {
            'oldest'      => $query->oldest(),
            'name_asc'    => $query->orderBy('name', 'asc'),
            'name_desc'   => $query->orderBy('name', 'desc'),
            'most_orders' => $query->orderByDesc('orders_count'),
            'most_spend'  => $query->orderByDesc('total_spend'),
            default       => $query->latest(),
        };

        $users = $query->paginate(12)->withQueryString();

        // 3. Attach session metadata to each user
        $users->getCollection()->transform(function ($user) use ($sessionsByUser, $fifteenMinutesAgo) {
            $userSessions = $sessionsByUser->get($user->id);
            $latestSession = $userSessions ? $userSessions->first() : null;

            $isOnline = false;
            $lastActivityTime = null;
            $deviceInfo = 'Belum pernah login';

            if ($latestSession) {
                $lastActivityTimestamp = $latestSession->last_activity;
                $isOnline = $lastActivityTimestamp >= $fifteenMinutesAgo;
                $lastActivityTime = Carbon::createFromTimestamp($lastActivityTimestamp);
                $deviceInfo = $this->parseUserAgent($latestSession->user_agent);
            }

            $user->is_online = $isOnline;
            $user->last_activity_time = $lastActivityTime;
            $user->device_info = $deviceInfo;

            return $user;
        });

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Menampilkan detail riwayat aktivitas pengguna
     */
    public function show(User $user, Request $request): View|JsonResponse
    {
        $user->loadCount(['orders', 'reviews', 'shippingAddresses'])
            ->loadSum([
                'orders as total_spend' => function ($q) {
                    $q->whereIn('status', ['paid', 'in_production', 'shipped', 'completed']);
                }
            ], 'grand_total');

        $user->load([
            'orders' => function ($q) {
                $q->with('items.product')->latest()->take(5);
            },
            'shippingAddresses',
            'reviews.product',
            'premiumSubscriptions' => function ($q) {
                $q->latest()->take(3);
            }
        ]);

        $latestSession = null;
        if (Schema::hasTable('sessions')) {
            $latestSession = DB::table('sessions')
                ->where('user_id', $user->id)
                ->orderBy('last_activity', 'desc')
                ->first();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'user'    => $user,
                'session' => $latestSession,
            ]);
        }

        return view('admin.users.show', compact('user', 'latestSession'));
    }

    /**
     * Memperbarui peran (role) pengguna (Admin / Customer)
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,customer',
        ]);

        if ($user->id === Auth::id() && $validated['role'] !== 'admin') {
            return back()->with('error', 'Anda tidak dapat mengubah peran akun Anda sendiri menjadi Customer.');
        }

        $user->update(['role' => $validated['role']]);

        $roleLabel = $validated['role'] === 'admin' ? 'Administrator' : 'Customer';
        return back()->with('success', "Peran pengguna {$user->name} berhasil diubah menjadi {$roleLabel}.");
    }

    /**
     * Menghapus akun pengguna
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.');
        }

        $userName = $user->name;
        $user->delete();

        return back()->with('success', "Akun pengguna {$userName} berhasil dihapus dari sistem.");
    }

    /**
     * Helper sederhana untuk merangkum User-Agent menjadi nama browser dan OS yang mudah dibaca
     */
    protected function parseUserAgent(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'Perangkat tidak diketahui';
        }

        $os = 'Unknown OS';
        if (preg_match('/windows|win32/i', $userAgent)) $os = 'Windows';
        elseif (preg_match('/macintosh|mac os x/i', $userAgent)) $os = 'macOS';
        elseif (preg_match('/linux/i', $userAgent)) $os = 'Linux';
        elseif (preg_match('/android/i', $userAgent)) $os = 'Android';
        elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) $os = 'iOS';

        $browser = 'Browser';
        if (preg_match('/chrome|crios/i', $userAgent) && !preg_match('/edge|edg|opr|opera/i', $userAgent)) $browser = 'Chrome';
        elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome|crios/i', $userAgent)) $browser = 'Safari';
        elseif (preg_match('/firefox|fxios/i', $userAgent)) $browser = 'Firefox';
        elseif (preg_match('/edg/i', $userAgent)) $browser = 'Edge';
        elseif (preg_match('/opera|opr/i', $userAgent)) $browser = 'Opera';

        return "{$browser} ({$os})";
    }
}
