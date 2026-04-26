<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store( Request $request) {


    $cart =  Cart::where('user_id' ,auth()->id())
                   ->with('cartItems.variant' , 'cartItems.product')
                   ->first();

    if( !$cart || $cart->cartItems->isEmpty()){

    return response()->json([
        'status' => false,
        'message' => 'Cart is empty',
    ],422);

    }

     foreach($cart->cartItems as $cartItem){
        if($cartItem->variant->stock < $cartItem->quantity){

        return response()->json([
            'status'=> false,
            'message' => 'Not enough stock',
        ],422);
        }
         }
      DB::transaction(function () use ($cart) {

        $subTotal = $cart->cartItems->sum(fn($i) => $i->price  * $i->quantity);
        $total = $subTotal - 0 + 0;
        $order =  Order::create([
            'user_id' => auth()->id(),
            'address_id'  => auth()->user()->address()->where('is_default',true)->first()?->id,
            'status'=> 'pending',
            'sub_total' => $subTotal,
            'discount'=> 0,
            'shipping_cost' => 0,
            'total' => $total,
            'payment_method' => 'cash_on_delivery',
            'payment_status' => 'pending',


        ]);

            foreach($cart->cartItems as $cartItem){
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $cartItem->product_id,
            'variant_id' => $cartItem->variant_id,
            'quantity' => $cartItem->quantity,
            'price' => $cartItem->price,
            'product_name' => $cartItem->product->name,

        ]);
                $cartItem->variant->decrement('stock', $cartItem->quantity);

            }


    $cart->delete();


});

    return response()->json([
        'status'=> true,
        'message' => 'Order placed successfully',
    ], 201);

    }

    public function index(){

    $userOrders = Order::where('user_id', auth()->id())
                        ->with(['orderItems.product.primaryImage'])
                        ->latest()
                        ->simplePaginate(5);


                       return response()->json([
                        'status' => true ,
                        'message' => 'Orders retrieved successfully',
                        'data' => $userOrders,
                       ]);
    }

    public function show(Order $order){

            //    if user is customer and order belongs to another user
             if(auth()->user()->hasRole('customer') && $order->user_id !== auth()->id()){

                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                ],403);
             }

             $order->load('orderItems.product.primaryImage', 'address');



               return response()->json([
                'status' => true,
                'message' => 'Order retrieved successfully',
                'data' => $order,
               ],200);


    }

    public function Adminindex(Request $request){

    $orders = Order::with(['orderItems.product.primaryImage' , 'user' , 'address'])
                        ->when($request->status ,  fn($q) =>

                         $q->where('status',$request->status)

                         )
                         ->latest()
                         ->simplePaginate(5);


                       return response()->json([
                        'status' => true ,
                        'message' => 'Orders retrieved successfully',
                        'data' => $orders,
                       ]);


    }

    public function AdminUpdateStatus(Order $order , Request $request){


        $request->validate([

        'status' => 'required|in:' . implode(',', [

                    Order::STATUS_PENDING,
                    Order::STATUS_CONFIRMED,
                    Order::STATUS_PROCESSING,
                    Order::STATUS_SHIPPED,
                    Order::STATUS_DELIVERED,
                    Order::STATUS_CANCELLED,

]),
        ]);
        $order->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order status updated successfully',
            'data' => $order->load(['orderItems.product.primaryImage' , 'user' , 'address']),
           ],200);






    }
}


