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
     *
     * @param string $waybillId Nomor resi pengiriman
     * @param string $courierCode Kode kurir (sicepat, jne, jnt, dll)
     * @return array
     */
    public function getTracking(string $waybillId, string $courierCode = 'jnt'): array
    {
        return $this->request('GET', "trackings/{$waybillId}/couriers/{$courierCode}");
    }
}
