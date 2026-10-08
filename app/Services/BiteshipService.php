<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service BiteshipService
 * Mengintegrasikan Biteship Logistics API untuk:
 * 1. Pencarian area wilayah (kelurahan/kecamatan/kota) standar logistik
 * 2. Kalkulasi tarif multi-kurir (JNE, SiCepat, J&T, Anteraja, Gojek, Grab) secara real-time
 * 3. Pembuatan order pengiriman & booking pickup kurir otomatis
 * 4. Pelacakan resi / waybill secara live
 */
class BiteshipService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $originAreaId;
    protected float $originLat;
    protected float $originLng;
    protected string $originPostalCode;

    public function __construct()
    {
        $this->apiKey           = (string) config('services.biteship.api_key');
        $this->baseUrl          = rtrim((string) config('services.biteship.base_url', 'https://api.biteship.com/v1'), '/');
        $this->originAreaId     = (string) config('services.biteship.origin_area_id', 'IDNP6IDJB164');
        $this->originLat        = (float) config('services.biteship.origin_latitude', -6.225587);
        $this->originLng        = (float) config('services.biteship.origin_longitude', 106.800542);
        $this->originPostalCode = (string) config('services.biteship.origin_postal_code', '12190');
    }

    /**
     * Helper request HTTP ke Biteship API
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->timeout(15)
                ->send($method, $url, [
                    $method === 'GET' ? 'query' : 'json' => $data,
                ]);

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            Log::warning("Biteship API Error [{$endpoint}]: " . $response->body());
            return [
                'success' => false,
                'error'   => $response->json('message') ?? 'Gagal menghubungi server Biteship.',
                'status'  => $response->status(),
            ];
        } catch (Exception $e) {
            Log::error("Biteship Connection Exception [{$endpoint}]: " . $e->getMessage());
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * 1. Autocomplete Pencarian Wilayah / Area Maps API
     *
     * @param string $query Nama kelurahan, kecamatan, atau kota
     * @return array
     */
    public function searchAreas(string $query): array
    {
        if (strlen(trim($query)) < 3) {
            return [];
        }

        $res = $this->request('GET', 'maps/areas', [
            'countries' => 'ID',
            'input'     => $query,
            'type'      => 'single',
        ]);

        return $res['areas'] ?? [];
    }

    /**
     * 2. Kalkulasi Tarif Multi-Kurir
     *
     * @param string|null $originAreaId ID Area Toko/Gudang (opsional, fallback ke config)
     * @param string|null $destinationAreaId ID Area Alamat Pemesan
     * @param float|null $originLat Koordinat Toko
     * @param float|null $originLng Koordinat Toko
     * @param float|null $destLat Koordinat Pemesan (opsional untuk instant kurir)
     * @param float|null $destLng Koordinat Pemesan
     * @param array $items Daftar item yang dikirim [{name, value, weight, quantity}]
     * @param string $couriers Daftar kode kurir (default: 'jne,sicepat,jnt,anteraja,gojek,grab')
     * @return array Daftar opsi ongkos kirim kurir
     */
    public function getRates(
        ?string $originAreaId = null,
        ?string $destinationAreaId = null,
        ?float $originLat = null,
        ?float $originLng = null,
        ?float $destLat = null,
        ?float $destLng = null,
        array $items = [],
        string $couriers = 'jne,sicepat,jnt,anteraja,gojek,grab'
    ): array {
        $payload = [
            'origin_area_id'      => $originAreaId ?: $this->originAreaId,
            'destination_area_id' => $destinationAreaId,
            'origin_latitude'     => $originLat ?: $this->originLat,
            'origin_longitude'    => $originLng ?: $this->originLng,
            'couriers'            => $couriers,
            'items'               => !empty($items) ? $items : [
                [
                    'name'     => 'Jersey Apparel',
                    'value'    => 250000,
                    'weight'   => 250,
                    'quantity' => 1,
                ],
            ],
        ];

        if ($destLat && $destLng) {
            $payload['destination_latitude'] = $destLat;
            $payload['destination_longitude'] = $destLng;
        }

        $res = $this->request('POST', 'rates/couriers', $payload);

        return $res['pricing'] ?? [];
    }

    /**
     * 3. Booking Pickup Kurir / Pembuatan Order Pengiriman Resmi
     *
     * @param array $orderData Data pengirim, penerima, kurir, dan item
     * @return array
     */
    public function createOrder(array $orderData): array
    {
        return $this->request('POST', 'orders', $orderData);
    }

    /**
     * 4. Pelacakan Status Perjalanan Paket (Tracking Resi)
     * Mendukung pelacakan real via Biteship API & Sandbox Mock Simulator untuk development/demo
     *
     * @param string $waybillId Nomor resi pengiriman
     * @param string $courierCode Kode kurir (sicepat, jne, jnt, dll)
     * @return array
     */
    public function getTracking(string $waybillId, string $courierCode = 'jnt'): array
    {
        $waybillUpper = strtoupper(trim($waybillId));

        // 1. Cek apakah nomor resi menggunakan pola simulasi resmi (TEST-, DEMO-, MOCK-, SIM-)
        if (str_starts_with($waybillUpper, 'TEST-') || 
            str_starts_with($waybillUpper, 'DEMO-') || 
            str_starts_with($waybillUpper, 'MOCK-') ||
            str_starts_with($waybillUpper, 'SIM-')) {
            return $this->getMockTracking($waybillUpper, $courierCode);
        }

        // 2. Jalur Produksi / Real Biteship API
        $response = $this->request('GET', "trackings/{$waybillId}/couriers/{$courierCode}");

        if (empty($response['success'])) {
            $errorMsg = $response['error'] ?? 'Data pelacakan belum tersedia dari server kurir.';
            if (str_contains(strtolower($errorMsg), 'balance') || str_contains(strtolower($errorMsg), 'top up')) {
                $errorMsg = 'Paket terdaftar di sistem pengiriman J&T Express. Pelacakan langsung dapat dipantau melalui portal resmi J&T Express.';
            }

            return [
                'success'      => false,
                'waybill_id'   => $waybillId,
                'status'       => 'on_delivery',
                'courier'      => [
                    'company' => $courierCode,
                    'name'    => strtoupper($courierCode) === 'JNT' ? 'J&T Express' : strtoupper($courierCode),
                ],
                'error'        => $errorMsg,
                'external_url' => 'https://www.jet.co.id/track',
                'history'      => [],
            ];
        }

        return $response;
    }

    /**
     * Simulator data pelacakan resi realistis untuk tahap development / demo
     *
     * @param string $waybillId
     * @param string $courierCode
     * @return array
     */
    protected function getMockTracking(string $waybillId, string $courierCode = 'jnt'): array
    {
        $isDelivered = str_contains($waybillId, 'DELIVERED') || str_contains($waybillId, 'SELESAI') || str_contains($waybillId, 'SUCCESS');
        $isTransit   = str_contains($waybillId, 'TRANSIT') || str_contains($waybillId, 'KIRIM');
        $isPickup    = str_contains($waybillId, 'PICKUP') || str_contains($waybillId, 'DROP');

        // Jika nomor resi simulator tidak sesuai dengan format resmi yang disediakan
        if (!$isDelivered && !$isTransit && !$isPickup) {
            return [
                'success'    => false,
                'waybill_id' => $waybillId,
                'status'     => 'not_found',
                'error'      => 'Nomor resi simulasi tidak valid. Pilih salah satu: TEST-JNT-DELIVERED, TEST-JNT-TRANSIT, atau TEST-JNT-PICKUP.',
                'history'    => [],
            ];
        }

        $courierName = strtoupper($courierCode) === 'JNT' ? 'J&T Express' : strtoupper($courierCode);
        $now = now();

        if ($isDelivered) {
            $history = [
                [
                    'note'         => 'Paket telah diterima oleh YBS (Penerima yang bersangkutan). Terima kasih telah menggunakan layanan ' . $courierName . '.',
                    'service_type' => 'EZ',
                    'updated_at'   => $now->copy()->subMinutes(15)->format('Y-m-d H:i:s'),
                ],
                [
                    'note'         => 'Paket sedang dibawa oleh Kurir (Sprinter) menuju alamat tujuan pengantaran.',
                    'service_type' => 'EZ',
                    'updated_at'   => $now->copy()->subHours(2)->format('Y-m-d H:i:s'),
                ],
                [
                    'note'         => 'Paket telah tiba di Drop Point / Gateway Kota Tujuan.',
                    'service_type' => 'EZ',
                    'updated_at'   => $now->copy()->subHours(8)->format('Y-m-d H:i:s'),
                ],
                [
                    'note'         => 'Paket telah diberangkatkan dari Pusat Sortir Utama (Hub Transit).',
                    'service_type' => 'EZ',
                    'updated_at'   => $now->copy()->subHours(18)->format('Y-m-d H:i:s'),
                ],
                [
                    'note'         => 'Paket telah diserahkan oleh Pengirim (Ngizan Apparel) dan diproses di Drop Point Asal.',
                    'service_type' => 'EZ',
                    'updated_at'   => $now->copy()->subDay()->format('Y-m-d H:i:s'),
                ],
            ];

            return [
                'success'    => true,
                'waybill_id' => $waybillId,
                'courier'    => [
                    'company' => $courierCode,
                    'name'    => $courierName,
                ],
                'status'     => 'delivered',
                'history'    => $history,
                'is_mock'    => true,
            ];
        }

        if ($isTransit) {
            $history = [
                [
                    'note'         => 'Paket sedang dibawa oleh Kurir (Sprinter) menuju alamat tujuan.',
                    'service_type' => 'EZ',
                    'updated_at'   => $now->copy()->subMinutes(30)->format('Y-m-d H:i:s'),
                ],
                [
                    'note'         => 'Paket telah tiba di Drop Point / Gateway Kota Tujuan.',
                    'service_type' => 'EZ',
                    'updated_at'   => $now->copy()->subHours(4)->format('Y-m-d H:i:s'),
                ],
                [
                    'note'         => 'Paket telah diserahkan oleh Pengirim (Ngizan Apparel) di Drop Point Asal.',
                    'service_type' => 'EZ',
                    'updated_at'   => $now->copy()->subHours(12)->format('Y-m-d H:i:s'),
                ],
            ];

            return [
                'success'    => true,
                'waybill_id' => $waybillId,
                'courier'    => [
                    'company' => $courierCode,
                    'name'    => $courierName,
                ],
                'status'     => 'on_delivery',
                'history'    => $history,
                'is_mock'    => true,
            ];
        }

        // Pickup / Drop Point Awal
        $history = [
            [
                'note'         => 'Paket telah diserahkan oleh Pengirim (Ngizan Apparel) dan diproses di Drop Point Asal ' . $courierName . '.',
                'service_type' => 'EZ',
                'updated_at'   => $now->copy()->subMinutes(20)->format('Y-m-d H:i:s'),
            ],
        ];

        return [
            'success'    => true,
            'waybill_id' => $waybillId,
            'courier'    => [
                'company' => $courierCode,
                'name'    => $courierName,
            ],
            'status'     => 'picking_up',
            'history'    => $history,
            'is_mock'    => true,
        ];
    }

    /**
     * 5. Reverse Geocoding via OpenStreetMap (Nominatim API)
     * Mengambil komponen alamat (jalan, kelurahan, kecamatan, kota, kode pos) dari koordinat GPS
     *
     * @param float $lat Latitude
     * @param float $lng Longitude
     * @return array
     */
    public function reverseGeocode(float $lat, float $lng): array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'NgizanApparel/1.0 (contact@ngizanapparel.com)',
            ])
            ->timeout(6)
            ->get('https://nominatim.openstreetmap.org/reverse', [
                'format'         => 'jsonv2',
                'lat'            => $lat,
                'lon'            => $lng,
                'addressdetails' => 1,
            ]);

            if ($response->successful()) {
                $data = $response->json() ?? [];
                $address = $data['address'] ?? [];

                $road = $address['road'] ?? $address['pedestrian'] ?? $address['street'] ?? '';
                $houseNumber = $address['house_number'] ?? '';
                $roadWithNumber = trim(($road ?: '') . ($houseNumber ? ' No. ' . $houseNumber : ''));
                $block = $address['city_block'] ?? $address['neighbourhood'] ?? $address['residential'] ?? '';
                $village = $address['village'] ?? $address['suburb'] ?? $address['quarter'] ?? '';
                $district = $address['city_district'] ?? $address['suburb'] ?? $address['county'] ?? '';
                $city = $address['city'] ?? $address['town'] ?? $address['regency'] ?? $address['city_district'] ?? '';
                $state = $address['state'] ?? '';
                $postcode = (string) ($address['postcode'] ?? '');

                // Susun string alamat jalan presisi
                $roadParts = array_filter([$roadWithNumber, $block, $village]);
                $streetAddress = implode(', ', $roadParts);
                if (empty($streetAddress) && !empty($data['display_name'])) {
                    $streetAddress = $data['display_name'];
                }

                return [
                    'success'        => true,
                    'road'           => $road,
                    'village'        => $village,
                    'district'       => $district,
                    'city'           => $city,
                    'state'          => $state,
                    'postcode'       => $postcode,
                    'street_address' => $streetAddress,
                    'display_name'   => $data['display_name'] ?? '',
                ];
            }

            Log::warning("Nominatim Reverse Geocode failed: " . $response->body());
            return ['success' => false, 'error' => 'Gagal mengambil alamat dari OpenStreetMap.'];
        } catch (\Exception $e) {
            Log::error("Reverse geocode exception: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

