<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PosTransaction;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();

        $posStats = [
            'totalTransactions' => PosTransaction::where('status', 'COMPLETED')->count(),
            'totalRevenue' => PosTransaction::where('status', 'COMPLETED')->sum('total'),
            'todaySales' => PosTransaction::where('status', 'COMPLETED')
                ->where('created_at', '>=', $today)->sum('total'),
            'todayTransactions' => PosTransaction::where('status', 'COMPLETED')
                ->where('created_at', '>=', $today)->count(),
        ];

        $orderStats = [
            'totalOrders' => Order::count(),
            'totalRevenue' => Order::where('payment_status', 'PAID')->sum('total'),
            'pendingOrders' => Order::where('status', 'PENDING')->count(),
        ];

        $topProducts = Product::orderByDesc('total_sold')->take(10)->get();

        return view('admin.analytics.index', compact('posStats', 'orderStats', 'topProducts'));
    }
}
