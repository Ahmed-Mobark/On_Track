<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private const COLORS = [
        'أسود' => '#000000', 'أبيض' => '#FFFFFF', 'رمادي' => '#6B7280',
        'كحلي' => '#1E3A5F', 'أحمر' => '#DC2626', 'أزرق' => '#2563EB',
        'أخضر' => '#16A34A', 'بني' => '#92400E', 'بيج' => '#D4B896',
        'زيتي' => '#556B2F', 'عنابي' => '#800020', 'وردي' => '#EC4899',
        'برتقالي' => '#EA580C', 'أصفر' => '#EAB308', 'بنفسجي' => '#7C3AED',
        'تركواز' => '#06B6D4',
    ];

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
            'variants' => 'required|array|min:1',
        ]);

        $data = $request->only([
            'name', 'name_ar', 'description', 'description_ar',
            'sku', 'base_price', 'sale_price', 'gender',
        ]);
        $data['slug'] = Str::slug($request->name) . '-' . Str::random(5);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_best_seller'] = $request->boolean('is_best_seller');

        $product = Product::create($data);

        if ($request->categories) {
            $product->categories()->attach($request->categories);
        }

        // Create variants per color → sizes
        foreach ($request->variants as $color => $colorData) {
            $hex = self::COLORS[$color] ?? '#000000';
            $sizes = $colorData['sizes'] ?? [];
            $qty = (int) ($colorData['quantity'] ?? 10);

            foreach ($sizes as $size) {
                $product->variants()->create([
                    'size' => $size,
                    'color' => $color,
                    'color_hex' => $hex,
                    'quantity' => $qty,
                    'sku' => $request->sku . '-' . strtoupper(substr(md5($color), 0, 3)) . '-' . $size,
                ]);
            }
        }

        // Upload images per color
        $this->handleColorImages($request, $product);

        return redirect()->route('admin.products.index')->with('success', 'تم إضافة المنتج بنجاح');
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'variants', 'categories']);
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
            'name', 'name_ar', 'description', 'description_ar',
            'sku', 'base_price', 'sale_price', 'gender',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_best_seller'] = $request->boolean('is_best_seller');

        $product->update($data);

        if ($request->has('categories')) {
            $product->categories()->sync($request->categories ?? []);
        }

        // Update variant quantities
        if ($request->variant_qty) {
            foreach ($request->variant_qty as $variantId => $qty) {
                ProductVariant::where('id', $variantId)
                    ->where('product_id', $product->id)
                    ->update(['quantity' => max(0, (int) $qty)]);
            }
        }

        // Delete selected images
        if ($request->delete_images) {
            $images = ProductImage::whereIn('id', $request->delete_images)
                ->where('product_id', $product->id)
                ->get();

            foreach ($images as $image) {
                Storage::disk('public')->delete($image->url);
                $image->delete();
            }
        }

        // Upload images per color (edit page)
        $this->handleColorImages($request, $product);

        // Fallback: plain images
        $this->handleImageUpload($request, $product);

        return redirect()->route('admin.products.index')->with('success', 'تم تحديث المنتج بنجاح');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->url);
        }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'تم حذف المنتج');
    }

    private function handleColorImages(Request $request, Product $product): void
    {
        $colorImages = $request->file('color_images');
        if (!$colorImages) return;

        $sortOrder = $product->images()->max('sort_order') ?? -1;

        foreach ($colorImages as $colorName => $files) {
            $hex = self::COLORS[$colorName] ?? null;

            foreach ($files as $file) {
                $sortOrder++;
                $path = $file->store('products/' . $product->id, 'public');

                $product->images()->create([
                    'url' => $path,
                    'alt' => $product->name_ar ?? $product->name,
                    'color_name' => $colorName,
                    'color_hex' => $hex,
                    'sort_order' => $sortOrder,
                ]);
            }
        }
    }

    private function handleImageUpload(Request $request, Product $product): void
    {
        if (!$request->hasFile('images')) return;

        $sortOrder = $product->images()->max('sort_order') ?? -1;

        foreach ($request->file('images') as $file) {
            $sortOrder++;
            $path = $file->store('products/' . $product->id, 'public');

            $product->images()->create([
                'url' => $path,
                'alt' => $product->name_ar ?? $product->name,
                'sort_order' => $sortOrder,
            ]);
        }
    }
}
