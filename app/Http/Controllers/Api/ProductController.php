<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

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
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
