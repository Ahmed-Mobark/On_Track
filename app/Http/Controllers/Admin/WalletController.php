<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
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

        return back()->with('success', 'تم تحديث المحفظة بنجاح');
    }
}
