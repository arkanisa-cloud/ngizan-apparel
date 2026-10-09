<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service BinderbyteService
 * Mengintegrasikan Binderbyte Logistics Tracking API untuk pelacakan resi publik (khususnya J&T Express).
 * Dilengkapi dengan Smart Caching (Laravel Cache) dan Mock Simulator untuk development.
 */
class BinderbyteService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = (string) config('services.binderbyte.api_key', '');
        $this->baseUrl = rtrim((string) config('services.binderbyte.base_url', 'https://api.binderbyte.com/v1'), '/');
    }

    /**
     * Pelacakan Status Perjalanan Paket (Tracking Resi) via Binderbyte API
     *
     * @param string $waybillId Nomor resi pengiriman
     * @param string $courierCode Kode kurir (default: 'jnt')
     * @param bool $forceRefresh Jika true, abaikan cache dan fetch langsung dari API
     * @return array
     */
    public function getTracking(string $waybillId, string $courierCode = 'jnt', bool $forceRefresh = false): array
    {
        $waybillUpper = strtoupper(trim($waybillId));
        $courierCode  = strtolower(trim($courierCode ?: 'jnt'));

        if (empty($waybillUpper)) {
            return [
                'success'      => false,
                'waybill_id'   => '',
                'status'       => 'not_found',
                'status_raw'   => 'NOT_FOUND',
                'courier'      => ['company' => $courierCode, 'name' => 'J&T Express'],
                'history'      => [],
                'error'        => 'Nomor resi tidak boleh kosong.',
                'external_url' => 'https://www.jet.co.id/track',
            ];
        }

        // 1. Cek mode simulasi testing/development
        if (str_starts_with($waybillUpper, 'TEST-') || 
            str_starts_with($waybillUpper, 'DEMO-') || 
            str_starts_with($waybillUpper, 'MOCK-') ||
            str_starts_with($waybillUpper, 'SIM-')) {
            return $this->getMockTracking($waybillUpper, $courierCode);
        }

        // 2. Cek Cache jika tidak forceRefresh
        $cacheKey = "tracking:{$courierCode}:{$waybillUpper}";
        if (!$forceRefresh) {
            try {
                if (Cache::has($cacheKey)) {
                    return Cache::get($cacheKey);
                }
            } catch (\Throwable $e) {
                Log::warning("Binderbyte Cache read failed: " . $e->getMessage());
            }
        }

        // 3. Panggil Binderbyte API
        $url = $this->baseUrl . '/track';

        try {
            $response = Http::timeout(30)
                ->connectTimeout(15)
                ->withOptions([
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                    ],
                ])
                ->acceptJson()
                ->get($url, [
                    'api_key' => $this->apiKey,
                    'courier' => $courierCode,
                    'awb'     => $waybillUpper,
                ]);

            $json = $response->json() ?? [];
            $httpStatus = $response->status();

            if ($response->successful() && ($json['status'] ?? 0) === 200 && !empty($json['data'])) {
                $normalized = $this->normalizeSuccessResponse($json['data'], $waybillUpper, $courierCode);

                // Smart Caching TTL (Fail-safe)
                try {
                    $statusNormalized = $normalized['status'];
                    if ($statusNormalized === 'delivered') {
                        // Paket sudah sampai -> Cache 7 hari
                        Cache::put($cacheKey, $normalized, now()->addDays(7));
                    } else {
                        // Paket dalam perjalanan -> Cache 20 menit
                        Cache::put($cacheKey, $normalized, now()->addMinutes(20));
                    }
                } catch (\Throwable $ce) {
                    Log::warning("Binderbyte Cache write failed: " . $ce->getMessage());
                }

                return $normalized;
            }

            // Tangani respons error dari Binderbyte
            $errorMessage = $this->parseErrorMessage($httpStatus, $json);
            Log::warning("Binderbyte API Track Error [{$waybillUpper}]: HTTP {$httpStatus} - " . ($json['message'] ?? 'Unknown error'));

            $errorResult = [
                'success'      => false,
                'waybill_id'   => $waybillUpper,
                'status'       => 'on_delivery',
                'status_raw'   => 'UNKNOWN',
                'courier'      => [
                    'company' => $courierCode,
                    'name'    => $courierCode === 'jnt' ? 'J&T Express' : strtoupper($courierCode),
                ],
                'summary'      => null,
                'history'      => [],
                'error'        => $errorMessage,
                'external_url' => 'https://www.jet.co.id/track',
                'is_mock'      => false,
            ];

            // Cache pesan error sebentar (5 menit) agar tidak spam kuota API
            try {
                Cache::put($cacheKey, $errorResult, now()->addMinutes(5));
            } catch (\Throwable $ce) {
                // Ignore cache error
            }

            return $errorResult;

        } catch (Exception $e) {
            Log::error("Binderbyte Connection Exception [{$waybillUpper}]: " . $e->getMessage());

            return [
                'success'      => false,
                'waybill_id'   => $waybillUpper,
                'status'       => 'on_delivery',
                'status_raw'   => 'UNKNOWN',
                'courier'      => [
                    'company' => $courierCode,
                    'name'    => $courierCode === 'jnt' ? 'J&T Express' : strtoupper($courierCode),
                ],
                'summary'      => null,
                'history'      => [],
                'error'        => 'Gagal terhubung ke server pelacakan resi. Silakan pantau langsung via portal resmi J&T Express.',
                'external_url' => 'https://www.jet.co.id/track',
                'is_mock'      => false,
            ];
        }
    }

    /**
     * Normalisasi response sukses Binderbyte ke struktur seragam aplikasi
     */
    protected function normalizeSuccessResponse(array $data, string $waybillId, string $courierCode): array
    {
        $summary = $data['summary'] ?? [];
        $detail  = $data['detail'] ?? [];
        $rawHistories = $data['history'] ?? [];

        $rawStatus = strtoupper(trim($summary['status'] ?? ''));
        $status = 'on_delivery';

        if (str_contains($rawStatus, 'DELIVER') || str_contains($rawStatus, 'SELESAI') || str_contains($rawStatus, 'TERIMA')) {
            $status = 'delivered';
        } elseif (str_contains($rawStatus, 'PICKUP') || str_contains($rawStatus, 'DROP') || str_contains($rawStatus, 'PICK')) {
            $status = 'picking_up';
        }

        $formattedHistory = [];
        foreach ($rawHistories as $h) {
            $desc = $h['desc'] ?? ($h['note'] ?? ($h['message'] ?? ''));
            $loc  = $h['location'] ?? ($h['city'] ?? '');
            $date = $h['date'] ?? ($h['updated_at'] ?? '');

            $noteText = $desc;
            if ($loc && !str_contains(strtoupper($desc), strtoupper($loc))) {
                $noteText .= " ({$loc})";
            }

            $formattedHistory[] = [
                'note'         => $noteText,
                'message'      => $desc,
                'location'     => $loc,
                'service_type' => $summary['service'] ?? 'EZ',
                'updated_at'   => $date,
            ];
        }

        return [
            'success'      => true,
            'waybill_id'   => $waybillId,
            'courier'      => [
                'company' => $courierCode,
                'name'    => $courierCode === 'jnt' ? 'J&T Express' : ($summary['courier'] ?? strtoupper($courierCode)),
            ],
            'status'       => $status,
            'status_raw'   => $rawStatus,
            'summary'      => [
                'courier'     => $summary['courier'] ?? 'J&T Express',
                'service'     => $summary['service'] ?? 'EZ',
                'status'      => $rawStatus,
                'date'        => $summary['date'] ?? null,
                'desc'        => $summary['desc'] ?? null,
                'weight'      => $summary['weight'] ?? null,
                'origin'      => $detail['origin'] ?? null,
                'destination' => $detail['destination'] ?? null,
                'shipper'     => $detail['shipper'] ?? null,
                'receiver'    => $detail['receiver'] ?? null,
            ],
            'history'      => $formattedHistory,
            'error'        => null,
            'external_url' => 'https://www.jet.co.id/track',
            'is_mock'      => false,
        ];
    }

    /**
     * Terjemahkan pesan error Binderbyte menjadi kalimat yang ramah untuk user
     */
    protected function parseErrorMessage(int $status, array $json): string
    {
        $rawMsg = strtolower($json['message'] ?? '');

        if ($status === 400 || str_contains($rawMsg, 'not found') || str_contains($rawMsg, 'invalid')) {
            return 'Nomor resi belum terindeks di sistem server J&T Express. Mohon tunggu 1-3 jam setelah paket diserahkan ke kurir.';
        }

        if ($status === 429 || str_contains($rawMsg, 'limit') || str_contains($rawMsg, 'quota')) {
            return 'Batas kuota pelacakan API telah tercapai. Anda tetap dapat memantau status melalui portal resmi J&T Express.';
        }

        if ($status === 401 || str_contains($rawMsg, 'unauthorized') || str_contains($rawMsg, 'key')) {
            return 'Autentikasi API Binderbyte belum terkonfigurasi dengan benar.';
        }

        return $json['message'] ?? 'Data pelacakan saat ini belum tersedia dari server kurir J&T Express.';
    }

    /**
     * Simulator data pelacakan resi realistis untuk tahap development & automated tests
     */
    protected function getMockTracking(string $waybillId, string $courierCode = 'jnt'): array
    {
        $isDelivered = str_contains($waybillId, 'DELIVERED') || str_contains($waybillId, 'SELESAI') || str_contains($waybillId, 'SUCCESS');
        $isTransit   = str_contains($waybillId, 'TRANSIT') || str_contains($waybillId, 'KIRIM');
        $isPickup    = str_contains($waybillId, 'PICKUP') || str_contains($waybillId, 'DROP');

        if (!$isDelivered && !$isTransit && !$isPickup) {
            return [
                'success'      => false,
                'waybill_id'   => $waybillId,
                'status'       => 'not_found',
                'status_raw'   => 'NOT_FOUND',
                'courier'      => ['company' => $courierCode, 'name' => 'J&T Express'],
                'history'      => [],
                'error'        => 'Nomor resi simulasi tidak valid. Pilih salah satu: TEST-JNT-DELIVERED, TEST-JNT-TRANSIT, atau TEST-JNT-PICKUP.',
                'external_url' => 'https://www.jet.co.id/track',
                'is_mock'      => true,
            ];
        }

        $courierName = strtoupper($courierCode) === 'JNT' ? 'J&T Express' : strtoupper($courierCode);
        $now = now();

        if ($isDelivered) {
            $history = [
                [
                    'note'         => 'Paket telah diterima oleh YBS (Penerima yang bersangkutan). Terima kasih telah menggunakan layanan ' . $courierName . '.',
                    'message'      => 'Delivered to recipient',
                    'location'     => 'Kota Tujuan',
                    'service_type' => 'EZ',
                    'updated_at'   => $now->copy()->subMinutes(15)->format('Y-m-d H:i:s'),
                ],
                [
                    'note'         => 'Paket sedang dibawa oleh Kurir (Sprinter) menuju alamat tujuan pengantaran.',
                    'message'      => 'On with courier',
                    'location'     => 'Drop Point Tujuan',
                    'service_type' => 'EZ',
                    'updated_at'   => $now->copy()->subHours(2)->format('Y-m-d H:i:s'),
                ],
                [
                    'note'         => 'Paket telah tiba di Drop Point / Gateway Kota Tujuan.',
                    'message'      => 'Arrived at destination gateway',
                    'location'     => 'Gateway Tujuan',
                    'service_type' => 'EZ',
                    'updated_at'   => $now->copy()->subHours(8)->format('Y-m-d H:i:s'),
                ],
                [
                    'note'         => 'Paket telah diberangkatkan dari Pusat Sortir Utama (Hub Transit).',
                    'message'      => 'Departed from central hub',
                    'location'     => 'Hub Transit Pusat',
                    'service_type' => 'EZ',
                    'updated_at'   => $now->copy()->subHours(18)->format('Y-m-d H:i:s'),
                ],
                [
                    'note'         => 'Paket telah diserahkan oleh Pengirim (Ngizan Apparel) dan diproses di Drop Point Asal.',
                    'message'      => 'Parcel dropped off by sender',
                    'location'     => 'Drop Point Asal',
                    'service_type' => 'EZ',
                    'updated_at'   => $now->copy()->subDay()->format('Y-m-d H:i:s'),
                ],
            ];

            return [
                'success'      => true,
                'waybill_id'   => $waybillId,
                'courier'      => [
                    'company' => $courierCode,
                    'name'    => $courierName,
                ],
                'status'       => 'delivered',
                'status_raw'   => 'DELIVERED',
                'summary'      => [
                    'courier'     => $courierName,
                    'service'     => 'EZ',
                    'status'      => 'DELIVERED',
                    'date'        => $now->copy()->subMinutes(15)->format('Y-m-d H:i:s'),
                    'desc'        => 'Paket telah diterima oleh penerima yang bersangkutan',
                ],
                'history'      => $history,
                'error'        => null,
                'external_url' => 'https://www.jet.co.id/track',
                'is_mock'      => true,
            ];
        }

        if ($isTransit) {
            $history = [
                [
                    'note'         => 'Paket sedang dibawa oleh Kurir (Sprinter) menuju alamat tujuan.',
                    'message'      => 'Out for delivery',
                    'location'     => 'Drop Point Tujuan',
                    'service_type' => 'EZ',
                    'updated_at'   => $now->copy()->subMinutes(30)->format('Y-m-d H:i:s'),
                ],
                [
                    'note'         => 'Paket telah tiba di Drop Point / Gateway Kota Tujuan.',
                    'message'      => 'Arrived at destination gateway',
                    'location'     => 'Gateway Tujuan',
                    'service_type' => 'EZ',
                    'updated_at'   => $now->copy()->subHours(4)->format('Y-m-d H:i:s'),
                ],
                [
                    'note'         => 'Paket telah diserahkan oleh Pengirim (Ngizan Apparel) di Drop Point Asal.',
                    'message'      => 'Processed at origin drop point',
                    'location'     => 'Drop Point Asal',
                    'service_type' => 'EZ',
                    'updated_at'   => $now->copy()->subHours(12)->format('Y-m-d H:i:s'),
                ],
            ];

            return [
                'success'      => true,
                'waybill_id'   => $waybillId,
                'courier'      => [
                    'company' => $courierCode,
                    'name'    => $courierName,
                ],
                'status'       => 'on_delivery',
                'status_raw'   => 'ON PROCESS',
                'summary'      => [
                    'courier'     => $courierName,
                    'service'     => 'EZ',
                    'status'      => 'ON PROCESS',
                    'date'        => $now->copy()->subMinutes(30)->format('Y-m-d H:i:s'),
                    'desc'        => 'Sedang diantar oleh kurir menuju alamat tujuan',
                ],
                'history'      => $history,
                'error'        => null,
                'external_url' => 'https://www.jet.co.id/track',
                'is_mock'      => true,
            ];
        }

        // Pickup / Drop Point Awal
        $history = [
            [
                'note'         => 'Paket telah diserahkan oleh Pengirim (Ngizan Apparel) dan diproses di Drop Point Asal ' . $courierName . '.',
                'message'      => 'Picked up / Dropped off',
                'location'     => 'Drop Point Asal',
                'service_type' => 'EZ',
                'updated_at'   => $now->copy()->subMinutes(20)->format('Y-m-d H:i:s'),
            ],
        ];

        return [
            'success'      => true,
            'waybill_id'   => $waybillId,
            'courier'      => [
                'company' => $courierCode,
                'name'    => $courierName,
            ],
            'status'       => 'picking_up',
            'status_raw'   => 'PICKUP',
            'summary'      => [
                'courier'     => $courierName,
                'service'     => 'EZ',
                'status'      => 'PICKUP',
                'date'        => $now->copy()->subMinutes(20)->format('Y-m-d H:i:s'),
                'desc'        => 'Paket telah diproses di gerai drop point',
            ],
            'history'      => $history,
            'error'        => null,
            'external_url' => 'https://www.jet.co.id/track',
            'is_mock'      => true,
        ];
    }
}
