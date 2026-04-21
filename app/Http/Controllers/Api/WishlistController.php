<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    //

    public function index(){



    $wishLists = Wishlist::where('user_id', auth()->id())
                                ->with('product.primaryImage')
                                ->get();
if($wishLists->isEmpty()){

return response()->json([
    'status' => false,
    'message' => 'No wishlists found',
]);

}

    return response()->json([
        'status' => true ,
        'message' => 'Wishlists retrieved successfully',
        'data' => $wishLists
    ]);
    }

    public function store(Request $request){

       $request->validate([
        'product_id' => 'required|exists:products,id',
       ]);
       if(Wishlist::where('user_id' , auth()->id())->where('product_id', $request->product_id)->exists()){

       return response()->json([
        'status' => false,
        'message' => 'Product already in wishlist',
       ],409);
       }


        Wishlist::create([
            'product_id' => $request->product_id,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Product added to wishlist successfully',

        ], 201);


    }

    public function destroy($productId){

    // $request->validate([
    //     'product_id' => 'required|exists:products,id'
    // ]);

    $wishListItem = Wishlist::where('user_id', auth()->id())
           ->where('product_id', $productId)
           ->first();

      if (!$wishListItem) {
        return response()->json([
            'status'  => false,
            'message' => 'Product not found in wishlist',
        ], 404);
    }

    $wishListItem->delete();

    return response()->json([
        'status' => true,
        'message' => 'Product removed from wishlist successfully',

    ]);




    }
}
