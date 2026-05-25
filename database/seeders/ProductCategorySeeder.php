<?php

namespace Database\Seeders;

use App\Helpers\ImageHelper\ImageHelper;
use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Elektronik',
                'tagline' => 'Temukan berbagai Produk Elektronik terbaik',
                'description' => 'Kategori Produk Elektronik seperti SmartPhone, laptop dan gadget lainnya',
                'children' => [
                    [
                        'name' => 'SmartPhone',
                        'tagline' => 'SmartPhone terbaru dengan teknologi canggih',
                        'description' => 'Berbagai merk SmartPhone terbaru dengan spesifikasi tinggi'
                    ],
                    [
                        'name' => 'Laptop',
                        'tagline' => 'Lapttop terbaru dengan teknologi AI',
                        'description' => 'Koleksi laptop untuk gaming maupun pekerja kantoran'
                    ],
                    [
                        'name' => 'Aksesoris Gadget',
                        'tagline' => 'Lengkapi Gadget anda dengan aksesoris Terbaik',
                        'description' => 'Berbagai aksesoris untuk SmartPhone dan Laptop'
                    ],
                ]
            ],
            [
                'name' => 'Fashion',
                'tagline' => 'Temukan gaya Fashion terbaik Anda',
                'description' => 'Kategori Fashion untuk Pria dan Wanita',
                'children' => [
                    [
                        'name' => 'Pakaian Pria',
                        'tagline' => 'Koleksi pakaian pria terkini',
                        'description' => 'Berbagai pakaian pria untuk berbagai kesempatan'
                    ],
                    [
                        'name' => 'Pakaian Wanita',
                        'tagline' => 'Koleksi Pakaian Wanita terkini',
                        'description' => 'Berbagai Pakaian Wanita untuk berbagai kesempatan'
                    ],
                ]
            ],
            [
                'name' => 'Keseahtan & Kecantikan',
                'tagline' => 'Produk kesehatan dan kecantikan terbaik',
                'description' => 'Kategori produk kesehatan dan kecantikan',
                'children' => [
                    [
                        'name' => 'Skincare',
                        'tagline' => 'Produk perawatan kulit terbaik',
                        'description' => 'Berbagai produk perawatan kulit untuk wajah dan tubuh'
                    ],
                    [
                        'name' => 'Suplemen',
                        'tagline' => 'Suplemen kesehatan berkulitas',
                        'description' => 'Berbagai suplemen untuk menjaga kesehatan tubuh'
                    ],
                ]
            ]
        ];

        $imageHelper = new ImageHelper;

        foreach ($categories as $category) {
            $parent = ProductCategory::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'tagline' => $category['tagline'],
                'description' => $category['description'],
                'image' => $imageHelper->storeAndResizeImage(
                    $imageHelper->createDummyImageWithTextSizeAndPosition(250, 250, 'center', 'center', 'random', 'medium'),
                    'buyer',
                    250,
                    250
                ),
                'parent_id' => null
            ]);

            foreach ($category['children'] as $child) {
                ProductCategory::create([
                'name' => $child['name'],
                'slug' => Str::slug($child['name']),
                'tagline' => $child['tagline'],
                'description' => $child['description'],
                'image' => $imageHelper->storeAndResizeImage(
                    $imageHelper->createDummyImageWithTextSizeAndPosition(250, 250, 'center', 'center', 'random', 'medium'),
                    'buyer',
                    250,
                    250
                ),
                'parent_id' => $parent->id
                ]);
            }
        }
    }
}
