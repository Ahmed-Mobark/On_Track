<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BostaService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.bosta.api_key', '');
        $this->baseUrl = rtrim(config('services.bosta.base_url', 'https://app.bosta.co'), '/');
    }

    /**
     * Create a delivery on Bosta when order is confirmed.
     */
    public function createDelivery(Order $order): array
    {
        $order->load(['user', 'address', 'items.product', 'items.variant']);

        $address = $order->address;
        if (!$address) {
            return ['success' => false, 'error' => 'لا يوجد عنوان للطلب'];
        }

        // Resolve Bosta district
        $district = $this->resolveDistrict($address->governorate, $address->city);

        // Build items description
        $itemsDesc = $order->items->map(function ($item) {
            $name = $item->product->name_ar ?? $item->product->name;
            return "{$name} ({$item->variant->size}/{$item->variant->color}) x{$item->quantity}";
        })->implode(' | ');

        // Calculate COD: total minus everything already paid (deposit + wallet)
        $alreadyPaid = (float) ($order->deposit_amount ?? 0) + (float) ($order->wallet_used ?? 0);
        $codAmount = max(0, (float) $order->total - $alreadyPaid);

        $dropOffAddress = [
            'city' => $district['cityName'] ?? $address->governorate,
            'firstLine' => $address->address ?? 'عنوان العميل',
        ];

        // Use districtId if found, otherwise fallback to districtName + cityId
        if (!empty($district['districtId'])) {
            $dropOffAddress['districtId'] = $district['districtId'];
        } elseif (!empty($district['cityId'])) {
            $dropOffAddress['cityId'] = $district['cityId'];
            $dropOffAddress['districtName'] = $address->city ?? $address->governorate;
        }

        $payload = [
            'type' => 10, // SEND
            'specs' => [
                'packageType' => 'Parcel',
                'size' => 'MEDIUM',
                'packageDetails' => [
                    'itemsCount' => $order->items->sum('quantity'),
                    'description' => mb_substr($itemsDesc, 0, 200),
                ],
            ],
            'notes' => $order->notes ?? '',
            'cod' => $codAmount,
            'dropOffAddress' => $dropOffAddress,
            'receiver' => [
                'firstName' => $address->first_name ?? $order->user?->first_name ?? '',
                'lastName' => $address->last_name ?? $order->user?->last_name ?? '',
                'phone' => $address->phone ?? $order->user?->phone ?? '',
                'email' => $order->user?->email ?? '',
            ],
            'businessReference' => $order->order_number,
            'allowToOpenPackage' => true,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/api/v2/deliveries?apiVersion=1", $payload);

            if ($response->successful()) {
                $data = $response->json();
                $trackingNumber = $data['data']['trackingNumber'] ?? null;

                $order->update([
                    'shipping_company' => 'Bosta',
                    'tracking_number' => $trackingNumber,
                    'shipment_status' => 'AWAITING_PICKUP',
                ]);

                return [
                    'success' => true,
                    'tracking_number' => $trackingNumber,
                    'bosta_id' => $data['data']['_id'] ?? null,
                ];
            }

            $error = $response->json('message') ?? $response->body();
            Log::error('Bosta create delivery failed', [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
                'order' => $order->order_number,
            ]);

            return ['success' => false, 'error' => $error];
        } catch (\Exception $e) {
            Log::error('Bosta API error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Track a delivery by tracking number.
     */
    public function trackDelivery(string $trackingNumber): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->get("{$this->baseUrl}/api/v2/deliveries/business/{$trackingNumber}");

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get all districts from Bosta (cached for 24h).
     */
    public function getAllDistricts(): array
    {
        return Cache::remember('bosta_districts', 86400, function () {
            try {
                $response = Http::get("{$this->baseUrl}/api/v2/cities/getAllDistricts");
                if ($response->successful()) {
                    return $response->json('data') ?? [];
                }
            } catch (\Exception $e) {
                Log::error('Bosta getAllDistricts failed: ' . $e->getMessage());
            }
            return [];
        });
    }

    /**
     * Resolve Bosta districtId from our governorate/city names.
     */
    private function resolveDistrict(string $governorate, ?string $city): array
    {
        $allDistricts = $this->getAllDistricts();

        foreach ($allDistricts as $bostaCity) {
            $cityNameAr = $bostaCity['cityOtherName'] ?? '';
            $cityName = $bostaCity['cityName'] ?? '';

            // Match governorate to Bosta city (Arabic or English)
            if (!$this->fuzzyMatch($governorate, $cityNameAr) && !$this->fuzzyMatch($governorate, $cityName)) {
                continue;
            }

            // Found the city, now find the district
            $cityId = $bostaCity['cityId'] ?? null;
            $districts = $bostaCity['districts'] ?? [];

            if ($city) {
                foreach ($districts as $d) {
                    $dNameAr = $d['districtOtherName'] ?? '';
                    $dName = $d['districtName'] ?? '';

                    if ($this->fuzzyMatch($city, $dNameAr) || $this->fuzzyMatch($city, $dName)) {
                        return [
                            'cityId' => $cityId,
                            'cityName' => $cityName,
                            'districtId' => $d['districtId'] ?? null,
                        ];
                    }
                }
            }

            // Fallback: first district of the city
            if (!empty($districts[0])) {
                return [
                    'cityId' => $cityId,
                    'cityName' => $cityName,
                    'districtId' => $districts[0]['districtId'] ?? null,
                ];
            }

            return ['cityId' => $cityId, 'cityName' => $cityName, 'districtId' => null];
        }

        return ['cityId' => null, 'cityName' => $governorate, 'districtId' => null];
    }

    private function fuzzyMatch(string $a, string $b): bool
    {
        $a = trim($a);
        $b = trim($b);
        if (!$a || !$b) return false;
        return mb_stripos($a, $b) !== false || mb_stripos($b, $a) !== false;
    }
}
