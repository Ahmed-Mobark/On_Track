<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'CUSTOMER')->withCount('orders');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        $customers = $query->latest()->paginate(20);
        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $user)
    {
        $user->loadCount('orders');
        $orders = $user->orders()->with('items.product')->latest()->take(10)->get();
        $wallet = $user->getOrCreateWallet();
        $transactions = $wallet->transactions()->take(20)->get();
        $totalSpent = $user->orders()->whereIn('status', ['CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED'])->sum('total');

        return view('admin.customers.show', compact('user', 'orders', 'wallet', 'transactions', 'totalSpent'));
    }
}
