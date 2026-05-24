<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['images' => fn($q) => $q->orderBy('sort_order')->limit(1), 'categories']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            });
        }

        $products = $query->latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'base_price' => 'required|numeric|min:0',
            'categories' => 'nullable|array',
            'variants' => 'nullable|array',
        ]);

        $data = $request->only([
            'name', 'name_ar', 'description', 'description_ar', 'materials',
            'care_instructions', 'sku', 'base_price', 'sale_price', 'gender',
            'is_active', 'is_featured', 'is_best_seller', 'seo_title', 'seo_desc',
        ]);
        $data['slug'] = Str::slug($request->name) . '-' . Str::random(5);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_best_seller'] = $request->boolean('is_best_seller');

        $product = Product::create($data);

        if ($request->categories) {
            $product->categories()->attach($request->categories);
        }

        if ($request->variants) {
            foreach ($request->variants as $variant) {
                $product->variants()->create($variant);
            }
        }

        if ($request->tags) {
            foreach (explode(',', $request->tags) as $tag) {
                $product->tags()->create(['tag' => trim($tag)]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'تم إضافة المنتج بنجاح');
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'variants', 'categories', 'tags']);
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'base_price' => 'required|numeric|min:0',
        ]);

        $data = $request->only([
            'name', 'name_ar', 'description', 'description_ar', 'materials',
            'care_instructions', 'sku', 'base_price', 'sale_price', 'gender',
            'seo_title', 'seo_desc',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_best_seller'] = $request->boolean('is_best_seller');

        $product->update($data);

        if ($request->has('categories')) {
            $product->categories()->sync($request->categories ?? []);
        }

        return redirect()->route('admin.products.index')->with('success', 'تم تحديث المنتج بنجاح');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'تم حذف المنتج');
    }
}
