<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;







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
        ->when($request->filled('min_price') || $request->filled('max_price') || $request->filled('size') || $request->filled('color'),
            function ($q) use ($request) {


              $q->whereHas('variants', function($q2) use ($request){

              if($request->filled('min_price')){

              $q2->where('price', '>=', $request->filled('min_price'));
              }
              if($request->filled('max_price')){

              $q2->where('price', '<=', $request->filled('max_price'));
              }
              if($request->filled('size')){

              $q2->where('size', $request->filled('size'));
              }
              if($request->filled('color')){

              $q2->where('color', $request->filled('color'));
              }


              });


            }
            )

        ->when($request->is_featured, fn($q) =>
            $q->where('is_featured', true)
        )
        ->when($request->is_new_arrival, fn($q) =>
            $q->where('is_new_arrival', true)
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
        // dd($request->variants);

      $request->validate([
        'name' => 'required|string|max:255',
        'description'=> 'required|string',
        'brand'=> 'sometimes|string|max:255',
        'category_id'=> 'required|exists:categories,id',
        'is_featured'=> 'sometimes|boolean',
        'is_new_arrival' => 'sometimes|boolean',
        'images'=>'sometimes|array',
        'images.*'=> 'image|mimes:jpg,jpeg,png|max:2048',
        'variants' => 'required|array',
        'variants.*.size'   => 'required|in:XS,S,M,L,XL,XXL',
        'variants.*.color'  => 'required|string',
        'variants.*.price'  => 'required|numeric',
        'variants.*.stock'  => 'required|integer',

      ]);

       $existingProduct = Product::where('name' , $request->name)
       ->where('brand' , $request->brand)
       ->where('category_id' , $request->category_id)
       ->first();

       if($existingProduct){
         return response()->json([
        'status' => false,
        'message' => 'This product already exists',
        'data' => $existingProduct
    ], 409);
       }
        //  create product
       $product = Product::create([
        'name'           => $request->name,
        'brand'          => $request->brand,
        'category_id'    => $request->category_id,
        'slug'           => Str::slug($request->name) . '-' . uniqid(),
        'description'    => $request->description,
        'is_featured'    => $request->is_featured ?? false,
        'is_new_arrival' => $request->is_new_arrival ?? false,
        ]



    );

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

        foreach($request->variants as $variant){

          $size = strtoupper($variant['size']);
          $color = strtolower($variant['color']);

          $exists = $product->variants()
          ->where('size' ,$size)
          ->where('color' ,$color)
          ->exists();

          if($exists){
            // that's mean Skip this variant
            continue;

          }
          $product->variants()->create([
             'size'  => $size,
            'color' => $color,
            'price' => (float) $variant['price'],
            'stock' => (int) $variant['stock'],
          ]);
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
        'slug'=> 'sometimes|string|max:255|unique:products,slug,' . $product->id,
        'description'=> 'sometimes|string',
        'brand'=> 'sometimes|string|max:255',
        'category_id'=> 'sometimes|exists:categories,id',
        'is_featured'=> 'sometimes|boolean',
        'is_new_arrival' => 'sometimes|boolean',
        'images'=>'sometimes|array',
        'images.*'=> 'image|mimes:jpg,jpeg,png|max:2048',
        'variants' => 'sometimes|array',

      ]);

       $product->update($request->only([
        'name', 'slug', 'description',
        'brand', 'category_id',  'is_featured',
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
            [
            'price' => $variant['price'],
            'stock' => $variant['stock']],
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
