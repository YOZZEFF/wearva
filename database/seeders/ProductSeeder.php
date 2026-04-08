<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    $men   = Category::factory()->create(['name' => 'Men',   'slug' => 'men']);
    $women = Category::factory()->create(['name' => 'Women', 'slug' => 'women']);
    $kids  = Category::factory()->create(['name' => 'Kids',  'slug' => 'kids']);

    // Subcategories
    $shirts = Category::factory()->create(['name' => 'Shirts', 'slug' => 'shirts', 'parent_id' => $men->id]);
    $pants  = Category::factory()->create(['name' => 'Pants',  'slug' => 'pants',  'parent_id' => $men->id]);
    $dress  = Category::factory()->create(['name' => 'Dresses','slug' => 'dresses','parent_id' => $women->id]);

    // Products
    Product::factory(10)->create(['category_id' => $shirts->id]);
    Product::factory(10)->create(['category_id' => $pants->id]);
    Product::factory(10)->create(['category_id' => $dress->id]);
    }
}
