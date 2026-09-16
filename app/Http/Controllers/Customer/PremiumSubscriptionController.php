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
}
