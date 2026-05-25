<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with([
            'images' => fn($q) => $q->orderBy('sort_order')->limit(2),
            'categories',
            'variants',
        ]);

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "{$search}%")
                  ->orWhere('name_ar', 'like', "{$search}%");
            });
        }

        if ($request->category) {
            $query->whereHas('categories', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->gender) {
            $query->where('gender', 'like', '%"' . $request->gender . '"%');
        }

        if ($request->min_price) {
            $query->where('base_price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('base_price', '<=', $request->max_price);
        }

        if ($request->color || $request->size) {
            $query->whereHas('variants', function ($q) use ($request) {
                if ($request->color) $q->where('color', 'like', "%{$request->color}%");
                if ($request->size) $q->where('size', $request->size);
            });
        }

        $query->when($request->sort, function ($q, $sort) {
            return match ($sort) {
                'price_asc' => $q->orderBy('base_price', 'asc'),
                'price_desc' => $q->orderBy('base_price', 'desc'),
                'popular' => $q->orderByDesc('total_sold'),
                default => $q->latest(),
            };
        }, fn($q) => $q->latest());

        $products = $query->paginate(12)->withQueryString();

        $categories = \App\Models\Category::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('shop.partials.product-grid', compact('products'))->render(),
                'total' => $products->total(),
            ]);
        }

        return view('shop.products', compact('products', 'categories'));
    }

    public function show(string $slug)
    {
        $product = Product::active()->where('slug', $slug)->with([
            'images',
            'categories',
            'variants',
            'tags',
            'reviews' => fn($q) => $q->with('user')->latest()->take(10),
        ])->firstOrFail();

        // Related products (same categories)
        $categoryIds = $product->categories->pluck('id');
        $relatedProducts = Product::active()
            ->where('id', '!=', $product->id)
            ->whereHas('categories', fn($q) => $q->whereIn('categories.id', $categoryIds))
            ->with(['images' => fn($q) => $q->orderBy('sort_order')->limit(1), 'variants'])
            ->take(10)
            ->get();

        // If not enough, fill with random products
        if ($relatedProducts->count() < 10) {
            $more = Product::active()
                ->where('id', '!=', $product->id)
                ->whereNotIn('id', $relatedProducts->pluck('id'))
                ->with(['images' => fn($q) => $q->orderBy('sort_order')->limit(1), 'variants'])
                ->inRandomOrder()
                ->take(10 - $relatedProducts->count())
                ->get();
            $relatedProducts = $relatedProducts->merge($more);
        }

        return view('shop.product-detail', compact('product', 'relatedProducts'));
    }
}
