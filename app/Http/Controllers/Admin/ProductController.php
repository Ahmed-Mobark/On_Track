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
            $hex = $this->resolveColorHex($color, $colorData);
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

        // Create variants for newly added colors (edit page)
        if ($request->variants) {
            $existing = $product->variants()->get(['color', 'size'])
                ->map(fn ($v) => $v->color . '|' . $v->size)
                ->flip();

            foreach ($request->variants as $color => $colorData) {
                $hex = $this->resolveColorHex($color, $colorData);
                $sizes = $colorData['sizes'] ?? [];
                $qty = (int) ($colorData['quantity'] ?? 10);

                foreach ($sizes as $size) {
                    if ($existing->has($color . '|' . $size)) continue;

                    $product->variants()->create([
                        'size' => $size,
                        'color' => $color,
                        'color_hex' => $hex,
                        'quantity' => $qty,
                        'sku' => $product->sku . '-' . strtoupper(substr(md5($color), 0, 3)) . '-' . $size,
                    ]);
                }
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

    public function toggleVisibility(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        $message = $product->is_active ? 'تم إظهار المنتج في المتجر' : 'تم إخفاء المنتج من المتجر';

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'is_active' => $product->is_active,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
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

        $variantsData = $request->variants ?? [];

        foreach ($colorImages as $colorName => $files) {
            $hex = $this->resolveColorHex($colorName, $variantsData[$colorName] ?? [], $product);

            foreach ($files as $file) {
                $sortOrder++;
                $path = $this->storeOptimizedImage($file, 'products/' . $product->id);

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
            $path = $this->storeOptimizedImage($file, 'products/' . $product->id);

            $product->images()->create([
                'url' => $path,
                'alt' => $product->name_ar ?? $product->name,
                'sort_order' => $sortOrder,
            ]);
        }
    }

    /**
     * Store an uploaded image, resizing/compressing large ones with GD.
     * Falls back to storing the original file if GD can't decode it (e.g. HEIC).
     */
    private function storeOptimizedImage($file, string $dir, int $maxDim = 2000, int $quality = 85): string
    {
        $disk = Storage::disk('public');
        $mime = $file->getMimeType();
        $ext = strtolower($file->getClientOriginalExtension());
        $srcPath = $file->getRealPath();

        // HEIC/HEIF can't be read by GD → convert to JPEG first (Imagick / sips / heif-convert)
        $heicTmp = null;
        $isHeic = str_contains((string) $mime, 'heic') || str_contains((string) $mime, 'heif')
            || in_array($ext, ['heic', 'heif']);
        if ($isHeic) {
            $heicTmp = $this->convertHeicToJpeg($srcPath);
            if ($heicTmp) {
                $srcPath = $heicTmp;
                $mime = 'image/jpeg';
            }
        }

        $loaders = [
            'image/jpeg' => 'imagecreatefromjpeg',
            'image/png'  => 'imagecreatefrompng',
            'image/webp' => 'imagecreatefromwebp',
            'image/gif'  => 'imagecreatefromgif',
        ];

        // Not a GD-decodable raster image (e.g. HEIC we couldn't convert) → store original as-is
        if (!extension_loaded('gd') || !isset($loaders[$mime]) || !function_exists($loaders[$mime])) {
            if ($heicTmp) @unlink($heicTmp);
            return $file->store($dir, 'public');
        }

        try {
            $src = @$loaders[$mime]($srcPath);
            if (!$src) {
                if ($heicTmp) @unlink($heicTmp);
                return $file->store($dir, 'public');
            }

            $w = imagesx($src);
            $h = imagesy($src);
            $scale = min(1, $maxDim / max($w, $h));

            // Already small and within size budget → store original untouched
            // (skip this shortcut for HEIC we converted, so we keep the JPEG, not the raw HEIC)
            if ($scale >= 1 && !$heicTmp && $file->getSize() <= 1.5 * 1024 * 1024) {
                return $file->store($dir, 'public');
            }

            $nw = max(1, (int) round($w * $scale));
            $nh = max(1, (int) round($h * $scale));

            $isPng = $mime === 'image/png';
            $dst = imagecreatetruecolor($nw, $nh);

            if ($isPng) {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
            } else {
                // Flatten any transparency onto white for JPEG output
                $white = imagecolorallocate($dst, 255, 255, 255);
                imagefilledrectangle($dst, 0, 0, $nw, $nh, $white);
            }

            imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

            $relPath = $dir . '/' . Str::uuid()->toString() . ($isPng ? '.png' : '.jpg');
            $tmp = tempnam(sys_get_temp_dir(), 'img');

            if ($isPng) {
                imagepng($dst, $tmp, 6);
            } else {
                imagejpeg($dst, $tmp, $quality);
            }

            $disk->put($relPath, file_get_contents($tmp));
            @unlink($tmp);
            if ($heicTmp) @unlink($heicTmp);

            return $relPath;
        } catch (\Throwable $e) {
            if ($heicTmp) @unlink($heicTmp);
            return $file->store($dir, 'public');
        }
    }

    /**
     * Convert a HEIC/HEIF file to a temporary JPEG using whatever tool is available.
     * Returns the temp JPEG path, or null if no converter succeeded.
     */
    private function convertHeicToJpeg(string $src): ?string
    {
        $base = tempnam(sys_get_temp_dir(), 'heic');
        $out = $base . '.jpg';
        @unlink($base);

        // 1) Imagick with HEIC support
        if (extension_loaded('imagick')) {
            try {
                if (!empty(\Imagick::queryFormats('HEIC')) || !empty(\Imagick::queryFormats('HEIF'))) {
                    $im = new \Imagick($src);
                    $im->setImageFormat('jpeg');
                    $im->setImageCompressionQuality(90);
                    $im->writeImage($out);
                    $im->clear();
                    if (is_file($out) && filesize($out) > 0) return $out;
                }
            } catch (\Throwable $e) {
                // fall through to CLI tools
            }
        }

        // 2) CLI converters (sips on macOS, heif-convert/magick/convert on Linux)
        $templates = [
            'sips -s format jpeg %s --out %s',
            'heif-convert -q 90 %s %s',
            'magick %s -quality 90 %s',
            'convert %s -quality 90 %s',
        ];
        foreach ($templates as $tpl) {
            $bin = strtok($tpl, ' ');
            $which = @shell_exec('command -v ' . escapeshellarg($bin) . ' 2>/dev/null');
            if (empty(trim((string) $which))) continue;

            @shell_exec(sprintf($tpl, escapeshellarg($src), escapeshellarg($out)) . ' 2>&1');
            if (is_file($out) && filesize($out) > 0) return $out;
        }

        @unlink($out);
        return null;
    }

    private function resolveColorHex(string $color, array $colorData = [], ?Product $product = null): string
    {
        $hex = $colorData['hex'] ?? self::COLORS[$color] ?? null;

        if (is_string($hex) && preg_match('/^#[0-9A-Fa-f]{6}$/', $hex)) {
            return strtoupper($hex);
        }

        if ($product) {
            $fromVariant = $product->variants()->where('color', $color)->value('color_hex');
            if ($fromVariant) {
                return strtoupper($fromVariant);
            }
        }

        return self::COLORS[$color] ?? '#000000';
    }
}
