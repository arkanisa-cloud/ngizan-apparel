<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\PremiumSubscription;
use App\Services\MidtransService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PremiumSubscriptionController extends Controller
{
    /**
     * Memulai transaksi langganan Ngizan Premium (100k/thn)
     */
    public function store(Request $request, MidtransService $midtrans): JsonResponse
    {
        $user = Auth::user();

        try {
            $subscriptionCode = 'PREM-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $subscription = PremiumSubscription::create([
                'user_id'           => $user->id,
                'subscription_code' => $subscriptionCode,
                'amount'            => 100000.00,
                'duration_days'     => 365,
                'payment_status'    => 'pending',
            ]);

            $snapResult = $midtrans->createSubscriptionSnapToken($user, $subscription);

            return response()->json([
                'success'      => true,
                'subscription_code' => $subscriptionCode,
                'snap_token'   => $snapResult['snap_token'],
                'redirect_url' => $snapResult['redirect_url'],
                'message'      => 'Token pembayaran langganan berhasil dibuat.',
            ]);
        } catch (Exception $e) {
            Log::error('Gagal inisiasi langganan premium: ' . $e->getMessage(), ['user_id' => $user->id]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat sesi pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sinkronisasi status langganan premium setelah pembayaran Snap
     */
    public function sync(Request $request, MidtransService $midtrans): JsonResponse
    {
        $user = Auth::user();
        $subscriptionCode = $request->input('subscription_code');

        if ($subscriptionCode) {
            $subscription = PremiumSubscription::where('subscription_code', $subscriptionCode)->first();
        } else {
            $subscription = PremiumSubscription::where('user_id', $user->id)
                ->where('payment_status', 'pending')
                ->latest()
                ->first();
        }

        if (!$subscription) {
            return response()->json([
                'success'    => true,
                'is_premium' => $user->isPremiumActive(),
                'message'    => 'Tidak ada langganan pending.',
            ]);
        }

        $synced = $midtrans->syncSubscriptionStatus($subscription);
        $user->refresh();

        return response()->json([
            'success'       => true,
            'synced'        => $synced,
            'is_premium'    => $user->isPremiumActive(),
            'premium_until' => $user->premium_until?->translatedFormat('d F Y'),
            'message'       => $user->isPremiumActive() ? 'Keanggotaan Ngizan Premium Anda telah aktif!' : 'Menunggu konfirmasi pembayaran.',
        ]);
    }
}
