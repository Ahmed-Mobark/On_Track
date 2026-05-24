<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\WishlistItem;
use App\Models\PosTransaction;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();

        $stats = [
            'totalOrders' => Order::count(),
            'totalRevenue' => Order::where('payment_status', 'PAID')->sum('total'),
            'pendingOrders' => Order::where('status', 'PENDING')->count(),
            'todayOrders' => Order::where('created_at', '>=', $today)->count(),
            'totalProducts' => Product::count(),
            'totalCustomers' => User::where('role', 'CUSTOMER')->count(),
            'todayPOSSales' => PosTransaction::where('status', 'COMPLETED')
                ->where('created_at', '>=', $today)->sum('total'),
            'totalWishlistItems' => WishlistItem::count(),
        ];

        $recentOrders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Most wishlisted products
        $mostWishlisted = WishlistItem::select('product_id', DB::raw('count(*) as wishlist_count'))
            ->groupBy('product_id')
            ->orderByDesc('wishlist_count')
            ->take(5)
            ->with('product:id,name,name_ar,slug')
            ->get();

        // Best selling products
        $bestSelling = DB::table('order_items')
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(price * quantity) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get()
            ->map(function ($item) {
                $item->product = Product::select('id', 'name', 'name_ar', 'slug')->find($item->product_id);
                return $item;
            })
            ->filter(fn($item) => $item->product);

        return view('admin.dashboard', compact('stats', 'recentOrders', 'mostWishlisted', 'bestSelling'));
    }
}
