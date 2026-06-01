<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function update(Request $request, User $user)
    {
        $request->validate([
            'action' => 'required|in:add_balance,deduct_balance,add_points,deduct_points',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
        ]);

        $wallet = $user->getOrCreateWallet();
        $amount = (float) $request->amount;
        $reason = $request->reason ?: 'تعديل يدوي من الإدارة';

        match ($request->action) {
            'add_balance' => $wallet->addBalance($amount, $reason, 'Admin'),
            'deduct_balance' => $wallet->deductBalance($amount, $reason, 'Admin'),
            'add_points' => $wallet->addPoints((int) $amount, $reason, 'Admin'),
            'deduct_points' => $wallet->deductPoints((int) $amount, $reason, 'Admin'),
        };

        // Send notification to user
        $notificationService = app(NotificationService::class);
        match ($request->action) {
            'add_balance' => $notificationService->walletCredited($user, $amount, $reason),
            'deduct_balance' => $notificationService->walletDebited($user, $amount, $reason),
            'add_points' => $notificationService->send($user, "تم إضافة {$amount} نقطة", "تم إضافة " . (int)$amount . " نقطة لحسابك. السبب: {$reason}", 'points', ['url' => url('/account')]),
            'deduct_points' => $notificationService->send($user, "تم خصم {$amount} نقطة", "تم خصم " . (int)$amount . " نقطة من حسابك. السبب: {$reason}", 'points', ['url' => url('/account')]),
        };

        return back()->with('success', 'تم تحديث المحفظة بنجاح');
    }
}
