<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class NotificationService
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    // ========== Core ==========

    public function send(User $user, string $title, string $message, string $type = 'general', array $pushData = []): Notification
    {
        $link = $pushData['url'] ?? null;

        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'is_read' => false,
            'link' => $link,
        ]);

        // Send push notification if user has FCM token
        if ($user->fcm_token) {
            $this->firebase->sendToToken($user->fcm_token, $title, $message, $pushData);
        }

        return $notification;
    }

    public function sendToAll(string $title, string $message, string $type = 'general', array $pushData = []): int
    {
        $users = User::where('role', 'CUSTOMER')->where('is_active', true)->get();
        $count = 0;

        foreach ($users as $user) {
            $this->send($user, $title, $message, $type, $pushData);
            $count++;
        }

        return $count;
    }

    public function sendToAdmins(string $title, string $message, string $type = 'admin', array $pushData = []): void
    {
        $admins = User::whereIn('role', ['ADMIN', 'SUPER_ADMIN'])->where('is_active', true)->get();
        foreach ($admins as $admin) {
            $this->send($admin, $title, $message, $type, $pushData);
        }
    }

    // ========== Order Notifications ==========

    public function orderStatusChanged(Order $order, string $newStatus): void
    {
        if (!$order->user) return;

        $statusLabels = [
            'CONFIRMED' => 'تم تأكيد',
            'PROCESSING' => 'جاري تجهيز',
            'SHIPPED' => 'تم شحن',
            'DELIVERED' => 'تم توصيل',
            'CANCELLED' => 'تم إلغاء',
            'RETURNED' => 'تم إرجاع',
        ];

        $label = $statusLabels[$newStatus] ?? $newStatus;
        $title = "{$label} طلبك #{$order->order_number}";
        $message = $this->getOrderStatusMessage($order, $newStatus);

        $this->send($order->user, $title, $message, 'order', [
            'order_id' => $order->id,
            'url' => url("/orders/{$order->id}"),
        ]);
    }

    private function getOrderStatusMessage(Order $order, string $status): string
    {
        return match ($status) {
            'CONFIRMED' => "تم تأكيد طلبك #{$order->order_number} وجاري التجهيز. شكراً لثقتك في ON TRACK!",
            'PROCESSING' => "طلبك #{$order->order_number} قيد التجهيز وهيكون جاهز للشحن قريب.",
            'SHIPPED' => "طلبك #{$order->order_number} في الطريق إليك!" . ($order->tracking_number ? " رقم التتبع: {$order->tracking_number}" : ""),
            'DELIVERED' => "تم توصيل طلبك #{$order->order_number} بنجاح. نتمنى تكون سعيد بمنتجاتك!",
            'CANCELLED' => "تم إلغاء طلبك #{$order->order_number}. لو عندك أي استفسار تواصل معانا.",
            'RETURNED' => "تم تسجيل إرجاع طلبك #{$order->order_number}. المبلغ هيتم إضافته للمحفظة.",
            default => "تم تحديث حالة طلبك #{$order->order_number}.",
        };
    }

    // ========== Wallet & Points Notifications ==========

    public function pointsEarned(User $user, int $points, string $orderNumber): void
    {
        $this->send($user, "كسبت {$points} نقطة!", "تم إضافة {$points} نقطة لحسابك من طلب #{$orderNumber}. اجمع نقاط أكتر واستبدلها بخصومات!", 'points', [
            'url' => url('/account'),
        ]);
    }

    public function walletCredited(User $user, float $amount, string $reason): void
    {
        $formatted = number_format($amount, 2);
        $this->send($user, "تم إضافة {$formatted} ج.م للمحفظة", "تم إضافة {$formatted} ج.م لمحفظتك. السبب: {$reason}", 'wallet', [
            'url' => url('/account'),
        ]);
    }

    public function walletDebited(User $user, float $amount, string $reason): void
    {
        $formatted = number_format($amount, 2);
        $this->send($user, "تم خصم {$formatted} ج.م من المحفظة", "تم خصم {$formatted} ج.م من محفظتك. السبب: {$reason}", 'wallet', [
            'url' => url('/account'),
        ]);
    }

    // ========== Inventory Notifications (Admin) ==========

    public function checkLowStock(int $threshold = 5): Collection
    {
        $lowStockVariants = ProductVariant::with('product')
            ->where('quantity', '<=', $threshold)
            ->where('quantity', '>', 0)
            ->get();

        if ($lowStockVariants->isNotEmpty()) {
            $count = $lowStockVariants->count();
            $message = "يوجد {$count} منتج/منتجات قاربت على النفاد (أقل من {$threshold} قطع).";
            $details = $lowStockVariants->take(5)->map(function ($v) {
                $name = $v->product->name_ar ?? $v->product->name ?? 'منتج';
                return "- {$name} ({$v->size}/{$v->color}): {$v->quantity} قطعة";
            })->implode("\n");

            $this->sendToAdmins("تنبيه مخزون منخفض", $message . "\n" . $details, 'inventory', [
                'url' => url('/admin/inventory'),
            ]);
        }

        $outOfStock = ProductVariant::with('product')
            ->where('quantity', '<=', 0)
            ->get();

        if ($outOfStock->isNotEmpty()) {
            $count = $outOfStock->count();
            $items = $outOfStock->take(5)->map(function ($v) {
                $name = $v->product->name_ar ?? $v->product->name ?? 'منتج';
                return "- {$name} ({$v->size}/{$v->color})";
            })->implode("\n");

            $this->sendToAdmins("تنبيه نفاد مخزون", "{$count} منتج/منتجات نفدت من المخزون:\n{$items}", 'inventory', [
                'url' => url('/admin/inventory'),
            ]);
        }

        return $lowStockVariants;
    }
}
