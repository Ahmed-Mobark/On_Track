<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HeroBanner;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $banners = HeroBanner::where('is_active', true)->orderBy('sort_order')->get();
        $categories = Category::where('is_active', true)->whereNull('parent_id')->orderBy('sort_order')->take(8)->get();
        $bestSellers = Product::active()->bestSeller()->with(['images' => fn($q) => $q->limit(1)])->orderByDesc('total_sold')->take(8)->get();
        $newArrivals = Product::active()->with(['images' => fn($q) => $q->limit(1)])->latest()->take(8)->get();

        // Load products per category for tabs
        $categoryProducts = [];
        foreach ($categories as $category) {
            $categoryProducts[$category->id] = Product::active()
                ->whereHas('categories', fn($q) => $q->where('categories.id', $category->id))
                ->with(['images' => fn($q) => $q->orderBy('sort_order')->limit(1)])
                ->take(8)
                ->get();
        }

        return view('shop.home', compact('banners', 'categories', 'bestSellers', 'newArrivals', 'categoryProducts'));
    }
}
