<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;



class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

    foreach ($products as $product) {

       ProductVariant::create([
    'product_id' => $product->id,
    'size' => 'S',
    'color' => 'Black',
    'price' => $product->price,
    'stock' => rand(5, 20),
]);

ProductVariant::create([
    'product_id' => $product->id,
    'size' => 'M',
    'color' => 'Black',
    'price' => $product->price,
    'stock' => rand(5, 20),
]);

ProductVariant::create([
    'product_id' => $product->id,
    'size' => 'L',
    'color' => 'White',
    'price' => $product->price + 50,
    'stock' => rand(2, 15),
]);
    }
    }
}
