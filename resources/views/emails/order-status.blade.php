<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#0a0a0a;font-family:'Segoe UI',Tahoma,sans-serif;">
    <div style="max-width:600px;margin:0 auto;padding:20px;">
        {{-- Header --}}
        <div style="text-align:center;padding:30px 0 20px;">
            <h1 style="color:#e63946;font-size:24px;margin:0;">On Track</h1>
        </div>

        {{-- Main Card --}}
        <div style="background:#141414;border-radius:16px;padding:32px;border:1px solid rgba(255,255,255,0.05);">
            @php
                $statusLabels = [
                    'CONFIRMED' => 'تم تأكيد طلبك',
                    'PROCESSING' => 'طلبك قيد التجهيز',
                    'SHIPPED' => 'تم شحن طلبك',
                    'DELIVERED' => 'تم توصيل طلبك',
                    'CANCELLED' => 'تم إلغاء طلبك',
                ];
                $statusLabel = $statusLabels[$newStatus] ?? 'تحديث على طلبك';
                $customerName = $order->user?->first_name ?? 'عميلنا';
            @endphp

            <h2 style="color:white;font-size:22px;margin:0 0 8px;text-align:center;">{{ $statusLabel }}</h2>
            <p style="color:rgba(255,255,255,0.4);font-size:14px;text-align:center;margin:0 0 24px;">
                طلب رقم #{{ $order->order_number }}
            </p>

            <p style="color:rgba(255,255,255,0.7);font-size:15px;line-height:1.8;">
                مرحباً {{ $customerName }}،
                <br>
                @if($newStatus === 'CONFIRMED')
                    تم تأكيد طلبك وجاري تجهيزه. هنبعتلك تحديث لما يتشحن.
                @elseif($newStatus === 'PROCESSING')
                    طلبك قيد التجهيز دلوقتي وهيتشحن قريب.
                @elseif($newStatus === 'SHIPPED')
                    طلبك اتشحن! تقدر تتابع الشحنة من اللينك تحت.
                    @if($order->shipping_company)
                        <br>شركة الشحن: <strong style="color:white;">{{ $order->shipping_company }}</strong>
                    @endif
                    @if($order->tracking_number)
                        <br>رقم التتبع: <strong style="color:white;direction:ltr;unicode-bidi:embed;">{{ $order->tracking_number }}</strong>
                    @endif
                @elseif($newStatus === 'DELIVERED')
                    طلبك وصل! نتمنى يعجبك. لو عندك أي مشكلة تواصل معانا.
                @elseif($newStatus === 'CANCELLED')
                    للأسف تم إلغاء طلبك. لو عندك استفسار تواصل معانا.
                @endif
            </p>

            {{-- Order Items --}}
            <div style="margin:24px 0;border-top:1px solid rgba(255,255,255,0.1);padding-top:20px;">
                @foreach($order->items as $item)
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
                        <span style="color:rgba(255,255,255,0.7);font-size:14px;">
                            {{ $item->product->name_ar ?? $item->product->name }}
                            <span style="color:rgba(255,255,255,0.3);">({{ $item->variant->size ?? '' }}/{{ $item->variant->color ?? '' }}) x{{ $item->quantity }}</span>
                        </span>
                        <span style="color:white;font-size:14px;">{{ number_format($item->price * $item->quantity) }} ج.م</span>
                    </div>
                @endforeach
                <div style="display:flex;justify-content:space-between;padding:12px 0 0;margin-top:8px;border-top:1px solid rgba(255,255,255,0.1);">
                    <span style="color:white;font-weight:bold;font-size:16px;">الإجمالي</span>
                    <span style="color:#e63946;font-weight:bold;font-size:16px;">{{ number_format($order->total) }} ج.م</span>
                </div>
            </div>

            {{-- Track Button --}}
            <div style="text-align:center;margin-top:24px;">
                <a href="{{ $trackingUrl }}" style="display:inline-block;background:#e63946;color:white;padding:14px 40px;border-radius:12px;text-decoration:none;font-weight:bold;font-size:16px;">
                    تتبع الطلب
                </a>
            </div>
        </div>

        {{-- Footer --}}
        <div style="text-align:center;padding:20px 0;color:rgba(255,255,255,0.2);font-size:12px;">
            <p>On Track - {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
