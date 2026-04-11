<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;




class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index(Request $request)
{
    $products = Product::query()
        ->with(['primaryImage', 'variants', 'category.subCategories'])
        ->when($request->search, fn($q) =>
            $q->where('name', 'like', '%' . $request->search . '%')
        )
        ->when($request->category, fn($q) =>
            $q->whereHas('category', fn($q) =>
                $q->where('slug', $request->category)
                  ->orWhereHas('subCategories', fn($q) =>
                      $q->where('slug', $request->category)
                  )
            )
        )
        ->when($request->min_price, fn($q) =>
            $q->where('price', '>=', $request->min_price)
        )
        ->when($request->max_price, fn($q) =>
            $q->where('price', '<=', $request->max_price)
        )
        ->when($request->is_featured, fn($q) =>
            $q->where('is_featured', true)
        )
        ->when($request->is_new_arrival, fn($q) =>
            $q->where('is_new_arrival', true)
        )
        ->when($request->size, fn($q) =>
            $q->whereHas('variants', fn($q) =>
                $q->where('size', $request->size)
            )
        )
        ->when($request->color, fn($q) =>
            $q->whereHas('variants', fn($q) =>
                $q->where('color', $request->color)
            )
        )
        ->latest()
        ->paginate(15);

    return response()->json([
        'status'  => true,
        'message' => 'Products retrieved successfully',
        'data'    => $products,
    ]);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

      $request->validate([
        'name' => 'required|string|max:255',
        'slug'=> 'required|string|max:255',
        'description'=> 'required|string',
        'price' => 'required|numeric',
        'sale_price'=> 'sometimes|numeric',
        'brand'=> 'sometimes|string|max:255',
        'category_id'=> 'required|exists:categories,id',
        'stock'=> 'required|integer|min:0',
        'is_featured'=> 'sometimes|boolean',
        'is_new_arrival' => 'sometimes|boolean',
        'images'=>'sometimes|array',
        'images.*'=> 'image|mimes:jpg,jpeg,png|max:2048',
        'variants' => 'sometimes|array',

      ]);
        //  create product
       $product = Product::create([
        'name', 'slug', 'description', 'price', 'sale_price',
        'brand', 'category_id', 'stock', 'is_featured',
        'is_new_arrival'

        ]);

        //  store images & primary image

        if($request->hasFile('images') ){

        foreach($request->file('images') as $index => $image){

        $product->images()->create([
            'image_path' => $image->store('products','public'),
            'is_primary' => $index === 0

        ]);

        }
        }

        //  store variants
        if($request->has('variants')){

        foreach($request->variants as $variant){

        $product->variants()->create($variant);
        }
        }

        return response()->json([

        'status' => true,
        'message' => 'product created successfully',
        'data'=> $product->load(['images', 'variants' , 'category.subCategories'])

        ]);



    }

    /**
     * Display the specified resource.
     */
   public function show(Product $product)
{
    $product->load(['images', 'variants', 'category.subCategories']);

    return response()->json([
        'status'  => true,
        'message' => 'Product retrieved successfully',
        'data'    => $product,
    ]);
}
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
        'name' => 'sometimes|string|max:255',
        'slug'=> 'sometimes|string|max:255',
        'description'=> 'sometimes|string',
        'price' => 'sometimes|numeric',
        'sale_price'=> 'sometimes|numeric',
        'brand'=> 'sometimes|string|max:255',
        'category_id'=> 'sometimes|exists:categories,id',
        'stock'=> 'sometimes|integer|min:0',
        'is_featured'=> 'sometimes|boolean',
        'is_new_arrival' => 'sometimes|boolean',
        'images'=>'sometimes|array',
        'images.*'=> 'image|mimes:jpg,jpeg,png|max:2048',
        'variants' => 'sometimes|array',

      ]);

       $product->update($request->only([
        'name', 'slug', 'description', 'price', 'sale_price',
        'brand', 'category_id', 'stock', 'is_featured',
        'is_new_arrival'

        ]));

         if($request->hasFile('images') ){

        foreach($request->file('images') as $index => $image){

        $product->images()->create([
            'image_path' => $image->store('products','public'),
            'is_primary' => $index === 0

        ]);

        }
        }


         if($request->has('variants')){

        foreach($request->variants as $variant){

        $product->variants()->updateOrCreate(
            ['size' => $variant['size'], 'color' => $variant['color']],
             $variant);
        }
        }

        return response()->json([
        'status'  => true,
        'message' => 'Product updated successfully',
        'data'    => $product->load(['images', 'variants', 'category.subCategories']),
    ]);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //

        foreach($product->images as $image){

        Storage::disk('public')->delete($image->image_path);



        }

        $product->delete();

          return response()->json([
        'status'  => true,
        'message' => 'Product deleted successfully',
    ]);
    }
}
