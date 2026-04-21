<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;


class CartController extends Controller
{
    //

    public function index(Request $request){

    // get the cart of the user

    $cart = Cart::firstOrCreate(['user_id'=> auth()->id()]);

    //   load the cart items
    $cart->load(['cartItems.product.primaryImage', 'cartItems.variant']);

    // calculate the total price
    $total =  $cart->cartItems->sum(function($item){

    return  (float) $item->price * (int)  $item->quantity;

    });
    //   return the cart & total
    return response()->json([
        'status'=> true,
        'message'=> 'cart retrived successfully',
        'data' =>
        [
            'cart' => $cart,
            'total' => $total,
        ],
    ]);

    }

    public function store(Request $request){

    // validate the request

    $request->validate([
        'slug' => 'required|exists:products,slug',
        'variant_id'=> 'required|exists:product_variants,id',
        'quantity'=> 'required|integer|min:1',
    ]);
    //  get the product

    $product = Product::where('slug', $request->slug)->firstOrFail();


    // get the cart of the user

    $cart = Cart::firstOrCreate([
    'user_id' => auth()->id()
]);

    //  checck if the product is already in the cart or not to change the quantity

    $cartItem = $cart->cartItems()
    ->where('product_id', $product->id)
    ->where('variant_id',$request->variant_id )
    ->first();

    //  check the product stock
    //  let's suppose cartItem is null so we check the quantity
    $existingQty = $cartItem->quantity ?? 0;

    $quantity =  (int) $request->quantity + (int) $existingQty ;
    //  quantity 3

        // make sure that variant belongs to the product

        $variant = $product->variants()->findOrFail($request->variant_id);



    if(  $variant->stock < $quantity){

    return response()->json([
        'status' => false,
        'message' => 'Not enough stock',
    ]);
    }

    if($cartItem){

    $cartItem->increment('quantity', $request->quantity);
    }else{
        $cart->cartItems()->create([

        'product_id' => $product->id,
        'variant_id'=> $request->variant_id,
        'quantity'=> $request->quantity,
        'price' => $variant->price,

        ]);
    }

    return response()->json([

    'status' => true,
    'message' => 'Product added to cart successfully',
    'data' => $cart->load(['cartItems.product', 'cartItems.variant']),

    ]);



    }

    public function update(Request $request , CartItem $cartItem){

    if($cartItem->cart->user_id !== auth()->id()){

         return response()->json([
            'status' => false ,
            'message' => 'Unauthorized',
         ],403);
    }

         $request->validate([
             'quantity' => 'required|integer|min:1',
         ]);

         $variant = $cartItem->variant;

         if($variant->stock < $request->quantity){

         return response()->json([
            'status' => false,
            'message' => 'Not enough stock',

         ],422);
         }

         $cartItem->update(['quantity' => $request->quantity]);

         return response()->json([
            'status' => 200,
            'message' => 'Cart item updated successfully',
            'data' => $cartItem->load(['product' , 'variant']),
         ],200);


    }

    public function destroy(CartItem $cartItem){

    if($cartItem->cart->user_id !== auth()->id()){

    return response()->json([
        'status' => false ,
        'message' => 'Unauthorized',
    ],403);
    }

    $cartItem->delete();

    return response()->json([
        'status'=> true ,
        'message' => 'Cart item deleted successfully',
    ]);



    }
}
