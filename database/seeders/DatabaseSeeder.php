<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        User::create([
            'email' => 'admin@ontrack.eg',
            'password' => 'password',
            'first_name' => 'Admin',
            'last_name' => 'OnTrack',
            'role' => 'SUPER_ADMIN',
        ]);

        User::create([
            'email' => 'cashier@ontrack.eg',
            'password' => 'password',
            'first_name' => 'Cashier',
            'last_name' => 'OnTrack',
            'role' => 'CASHIER',
        ]);

        User::create([
            'email' => 'customer@test.com',
            'password' => 'password',
            'first_name' => 'أحمد',
            'last_name' => 'محمد',
            'phone' => '01012345678',
            'role' => 'CUSTOMER',
        ]);

        // Categories
        $cats = [
            ['name' => 'T-Shirts', 'name_ar' => 'تيشيرتات'],
            ['name' => 'Shorts', 'name_ar' => 'شورتات'],
            ['name' => 'Hoodies', 'name_ar' => 'هوديز'],
            ['name' => 'Joggers', 'name_ar' => 'جوجرز'],
            ['name' => 'Tank Tops', 'name_ar' => 'تانك توب'],
            ['name' => 'Compression', 'name_ar' => 'كومبريشن'],
        ];

        $categoryIds = [];
        foreach ($cats as $cat) {
            $c = Category::create([
                'name' => $cat['name'],
                'name_ar' => $cat['name_ar'],
                'slug' => Str::slug($cat['name']),
                'is_active' => true,
            ]);
            $categoryIds[$cat['name']] = $c->id;
        }

        // Products
        $products = [
            [
                'name' => 'Premium Sport T-Shirt',
                'name_ar' => 'تيشيرت رياضي بريميوم',
                'sku' => 'OT-TSH-001',
                'base_price' => 299,
                'sale_price' => 249,
                'description_ar' => 'تيشيرت رياضي مصنوع من أجود أنواع القماش، خفيف ومريح للتمارين المكثفة',
                'is_best_seller' => true,
                'is_featured' => true,
                'category' => 'T-Shirts',
                'gender' => 'men',
            ],
            [
                'name' => 'Performance Shorts',
                'name_ar' => 'شورت أداء عالي',
                'sku' => 'OT-SHR-001',
                'base_price' => 349,
                'description_ar' => 'شورت رياضي بتقنية التبريد السريع، مثالي للجري والتمارين',
                'is_best_seller' => true,
                'category' => 'Shorts',
                'gender' => 'men',
            ],
            [
                'name' => 'Oversized Hoodie',
                'name_ar' => 'هودي أوفرسايز',
                'sku' => 'OT-HOD-001',
                'base_price' => 599,
                'sale_price' => 499,
                'description_ar' => 'هودي أوفرسايز بقماش فليس ناعم، مثالي للإحماء والتمارين في الجو البارد',
                'is_featured' => true,
                'category' => 'Hoodies',
                'gender' => 'unisex',
            ],
            [
                'name' => 'Slim Jogger Pants',
                'name_ar' => 'بنطلون جوجر سليم',
                'sku' => 'OT-JOG-001',
                'base_price' => 499,
                'sale_price' => 399,
                'description_ar' => 'بنطلون جوجر سليم فيت، مريح ومرن للتمارين والحياة اليومية',
                'is_best_seller' => true,
                'category' => 'Joggers',
                'gender' => 'men',
            ],
            [
                'name' => 'Athletic Tank Top',
                'name_ar' => 'تانك توب رياضي',
                'sku' => 'OT-TNK-001',
                'base_price' => 249,
                'description_ar' => 'تانك توب رياضي خفيف الوزن، مصمم للتمارين المكثفة',
                'is_featured' => true,
                'category' => 'Tank Tops',
                'gender' => 'men',
            ],
            [
                'name' => 'Compression Top',
                'name_ar' => 'توب ضغط رياضي',
                'sku' => 'OT-CMP-001',
                'base_price' => 399,
                'description_ar' => 'توب ضغط لتحسين الأداء ودعم العضلات أثناء التمرين',
                'is_best_seller' => true,
                'category' => 'Compression',
                'gender' => 'men',
            ],
            [
                'name' => 'Track Pants',
                'name_ar' => 'بنطلون تراك',
                'sku' => 'OT-TRK-001',
                'base_price' => 449,
                'description_ar' => 'بنطلون تراك بخامة مقاومة للعرق مع جيوب بسوستة',
                'category' => 'Joggers',
                'gender' => 'men',
            ],
            [
                'name' => 'Sports Bra',
                'name_ar' => 'سبورتس برا',
                'sku' => 'OT-BRA-001',
                'base_price' => 329,
                'sale_price' => 279,
                'description_ar' => 'سبورتس برا بدعم متوسط، مريح ومصمم للتمارين',
                'is_featured' => true,
                'category' => 'Compression',
                'gender' => 'women',
            ],
        ];

        $sizes = ['S', 'M', 'L', 'XL'];
        $colors = [
            ['name' => 'أسود', 'hex' => '#000000'],
            ['name' => 'أبيض', 'hex' => '#FFFFFF'],
            ['name' => 'رمادي', 'hex' => '#6B7280'],
        ];

        foreach ($products as $p) {
            $product = Product::create([
                'name' => $p['name'],
                'name_ar' => $p['name_ar'],
                'slug' => Str::slug($p['name']) . '-' . Str::random(4),
                'sku' => $p['sku'],
                'base_price' => $p['base_price'],
                'sale_price' => $p['sale_price'] ?? null,
                'description_ar' => $p['description_ar'] ?? null,
                'gender' => $p['gender'] ?? null,
                'is_active' => true,
                'is_featured' => $p['is_featured'] ?? false,
                'is_best_seller' => $p['is_best_seller'] ?? false,
            ]);

            // Attach category
            if (isset($categoryIds[$p['category']])) {
                $product->categories()->attach($categoryIds[$p['category']]);
            }

            // Create variants
            $variantNum = 1;
            foreach ($colors as $color) {
                foreach ($sizes as $size) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'size' => $size,
                        'color' => $color['name'],
                        'color_hex' => $color['hex'],
                        'quantity' => rand(5, 30),
                        'sku' => $p['sku'] . '-' . match($color['name']) { 'أسود' => 'BLK', 'أبيض' => 'WHT', 'رمادي' => 'GRY', default => 'C' . $variantNum } . '-' . $size,
                    ]);
                    $variantNum++;
                }
            }
        }
    }
}
