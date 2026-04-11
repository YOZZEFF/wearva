<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    public function store(Request $request, Product $product){

    $request->validate([
        'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        'is_primary'=> 'sometimes|boolean'
    ]);

    if($request->is_primary){
        $product->images()->update(['is_primary' => false]);
    }

    $image = $product->images()->create([

    'image_path'=> $request->file('image')->store('products','public'),
    'is_primary' => $request->is_primary ?? false
    ]);

    return response()->json([
        'status'  => true,
        'message' => 'Image uploaded successfully',
        'data'    => $image,
    ], 201);

    }

    public function setPrimary(Product $product, ProductImage $image){


    if($image->product_id !== $product->id){

    return response()->json([
        'status' => false ,
        'message' => 'Image Not Found'
    ],404);
    }


      $product->images()->update('is_primary',false);
      $image->update(['is_primary' => true]);

      return response()->json([
        'status' => true ,
        'message' => 'Primary image updated successfully',
        'data'=> $image,
      ]);


    }

    public function destroy(Product $product , ProductImage $image){

    if($image->product_id !== $product->id){

    return response()->json([
        'status' => false ,
        'message' => 'Image Not Found'
    ],404);
    }

    Storage::disk('public')->delete($image->image_path);
    $image->delete();

    return response()->json([
        'status' => true ,
        'message' => 'Image deleted successfully',
      ]);

    }





}
