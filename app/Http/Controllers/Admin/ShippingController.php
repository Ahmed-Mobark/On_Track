<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    private const BOSTA_GOV_MAP = [
        'القاهرة' => 'Cairo', 'الجيزة' => 'Giza', 'الإسكندرية' => 'Alexandria',
        'القليوبية' => 'Qalyubia', 'الشرقية' => 'Sharqia', 'الدقهلية' => 'Dakahlia',
        'البحيرة' => 'Beheira', 'الغربية' => 'Gharbia', 'المنوفية' => 'Monufia',
        'كفر الشيخ' => 'Kafr El Sheikh', 'الفيوم' => 'Fayoum', 'بني سويف' => 'Beni Suef',
        'المنيا' => 'Minya', 'أسيوط' => 'Assuit', 'سوهاج' => 'Sohag',
        'قنا' => 'Qena', 'الأقصر' => 'Luxor', 'أسوان' => 'Aswan',
        'الإسماعيلية' => 'Ismailia', 'السويس' => 'Suez', 'بورسعيد' => 'Port Said',
        'دمياط' => 'Damietta', 'شمال سيناء' => 'North Sinai', 'جنوب سيناء' => 'South Sinai',
        'البحر الأحمر' => 'Red Sea', 'مطروح' => 'Matrouh', 'الوادي الجديد' => 'New Valley',
    ];

    public function index()
    {
        $rates = ShippingRate::orderBy('governorate')->orderBy('city')->get();
        $grouped = $rates->groupBy('governorate');
        return view('admin.shipping.index', compact('rates', 'grouped'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'governorate' => 'required|string',
            'city' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'estimated_days' => 'nullable|integer|min:1',
        ]);

        ShippingRate::updateOrCreate(
            ['governorate' => $request->governorate, 'city' => $request->city ?: null],
            ['cost' => $request->cost, 'estimated_days' => $request->estimated_days, 'is_active' => true]
        );

        return back()->with('success', 'تم حفظ سعر الشحن');
    }

    public function update(Request $request, ShippingRate $shippingRate)
    {
        $request->validate([
            'cost' => 'required|numeric|min:0',
            'estimated_days' => 'nullable|integer|min:1',
        ]);

        $shippingRate->update([
            'cost' => $request->cost,
            'estimated_days' => $request->estimated_days,
        ]);

        return back()->with('success', 'تم تحديث سعر الشحن');
    }

    public function destroy(ShippingRate $shippingRate)
    {
        $shippingRate->delete();
        return back()->with('success', 'تم حذف سعر الشحن');
    }

    public function getCost(Request $request)
    {
        $cost = ShippingRate::getCost($request->governorate, $request->city);
        return response()->json(['cost' => $cost]);
    }

    /**
     * API: Get Bosta shipping fee for a governorate (called via AJAX from shipping form)
     */
    public function getBostaCost(Request $request)
    {
        $governorate = $request->governorate;
        $bostaCity = self::BOSTA_GOV_MAP[$governorate] ?? null;

        if (!$bostaCity) {
            return response()->json(['cost' => null, 'error' => 'محافظة غير معروفة']);
        }

        $apiKey = config('services.bosta.api_key');
        $baseUrl = rtrim(config('services.bosta.base_url', 'https://app.bosta.co'), '/');

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])->get("{$baseUrl}/api/v2/pricing/shipment/calculator", [
                'pickupCity' => 'Gharbia',
                'dropOffCity' => $bostaCity,
                'cod' => 0,
                'type' => 'SEND',
                'size' => 'Normal',
            ]);

            if ($response->successful()) {
                $data = $response->json('data');
                $cost = $data['shippingFee'] ?? $data['transit']['cost'] ?? null;
                if ($cost !== null) $cost = (int) ceil($cost / 10) * 10; // Round up to nearest 10
                return response()->json(['cost' => $cost]);
            }

            return response()->json(['cost' => null]);
        } catch (\Exception $e) {
            return response()->json(['cost' => null, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Sync all governorate shipping rates from Bosta
     */
    public function syncFromBosta()
    {
        $apiKey = config('services.bosta.api_key');
        $baseUrl = rtrim(config('services.bosta.base_url', 'https://app.bosta.co'), '/');

        if (!$apiKey) {
            return back()->with('error', 'مفتاح Bosta API غير مضبوط');
        }

        $synced = 0;
        $reverseMap = array_flip(self::BOSTA_GOV_MAP);

        foreach (self::BOSTA_GOV_MAP as $govAr => $bostaCity) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => $apiKey,
                ])->get("{$baseUrl}/api/v2/pricing/shipment/calculator", [
                    'pickupCity' => 'Gharbia',
                    'dropOffCity' => $bostaCity,
                    'cod' => 0,
                    'type' => 'SEND',
                    'size' => 'Normal',
                ]);

                if ($response->successful()) {
                    $data = $response->json('data');
                    $cost = $data['shippingFee'] ?? $data['transit']['cost'] ?? null;

                    if ($cost !== null) {
                        $cost = (int) ceil($cost / 10) * 10; // Round up to nearest 10
                        ShippingRate::updateOrCreate(
                            ['governorate' => $govAr, 'city' => null],
                            ['cost' => $cost, 'estimated_days' => 3, 'is_active' => true]
                        );
                        $synced++;
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Bosta pricing failed for {$bostaCity}: " . $e->getMessage());
            }
        }

        return back()->with('success', "تم مزامنة {$synced} سعر شحن من بوسطة. تقدر تعدل عليهم.");
    }
}
