<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Nike Air Max 270',
                'brand' => 'Nike',
                'category' => 'Lifestyle',
                'price' => 1299000,
                'original_price' => 1599000,
                'description' => 'Sepatu lifestyle modern dengan desain premium dan nyaman digunakan untuk aktivitas harian.',
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800',
                'sizes' => [39, 40, 41, 42, 43],
                'colors' => ['Black', 'White'],
                'is_new' => true,
                'is_trending' => true,
                'is_best_seller' => false,
            ],
            [
                'name' => 'Adidas Ultraboost 22',
                'brand' => 'Adidas',
                'category' => 'Running',
                'price' => 2199000,
                'original_price' => 2499000,
                'description' => 'Sepatu running premium dengan bantalan empuk dan desain modern.',
                'image' => 'https://images.unsplash.com/photo-1600185365778-7875a359b924?w=800',
                'sizes' => [40, 41, 42, 43, 44],
                'colors' => ['Black', 'Gray'],
                'is_new' => false,
                'is_trending' => true,
                'is_best_seller' => true,
            ],
            [
                'name' => 'Puma RS-X',
                'brand' => 'Puma',
                'category' => 'Casual',
                'price' => 999000,
                'original_price' => 1299000,
                'description' => 'Sepatu casual stylish untuk daily outfit dengan tampilan sporty.',
                'image' => 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=800',
                'sizes' => [39, 40, 41, 42],
                'colors' => ['White', 'Blue'],
                'is_new' => true,
                'is_trending' => false,
                'is_best_seller' => true,
            ],
        ];

        foreach ($products as $item) {
            $brand = Brand::where('name', $item['brand'])->first();
            $category = Category::where('name', $item['category'])->first();

            $product = Product::create([
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'name' => $item['name'],
                'slug' => Str::slug($item['name']),
                'sku' => strtoupper(Str::random(8)),
                'price' => $item['price'],
                'original_price' => $item['original_price'],
                'description' => $item['description'],
                'is_new' => $item['is_new'],
                'is_trending' => $item['is_trending'],
                'is_best_seller' => $item['is_best_seller'],
                'is_active' => true,
            ]);

            $product->images()->create([
                'image' => $item['image'],
                'is_main' => true,
            ]);

            foreach ($item['sizes'] as $size) {
                foreach ($item['colors'] as $color) {
                    $product->variants()->create([
                        'size' => $size,
                        'color' => $color,
                        'stock' => rand(5, 20),
                    ]);
                }
            }
        }
    }
}
