<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
     public function index(){

     $addresses = Address::where('user_id' , auth()->id())->get();
     if($addresses->isEmpty()){

     return response()->json([
        'status' => false,
        'message' => 'No addresses found',
     ]);
     }

     return response()->json([
        'status' => true,
        'message' => 'Addresses retrieved successfully',
        'data' => $addresses
     ]);

     }

     public function store(Request $request){

        $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'street' => 'required|string',
        'country' => 'required|string',
        'city' => 'required|string',
        'is_default' => 'required|boolean',
     ]);

     $existaddress = Address::where('user_id',auth()->id())
                                 ->where('street',$request->street)
                                 ->where('city',$request->city)
                                 ->first();

           if($existaddress){
            return response()->json([
                'status' => false,
                'message' => 'Address already exists',
            ]);
           }

     if($request->is_default){

        Address::where('user_id',auth()->id())
                                ->update(['is_default'=>false]);
     }

    $address = Address::create([

    'user_id' => auth()->id(),
    'name' => $request->name,
    'phone' => $request->phone,
    'street' => $request->street,
    'country' => $request->country,
    'city' => $request->city,
    'is_default' => $request->is_default,
    ]);

        return response()->json([
            'status' => true,
            'message' => 'Address created successfully',
            'data' => $address
        ]);

     }

     public function update(Request $request , Address $address){

     $request->validate([
        'name' => 'sometimes|string|max:255',
        'phone' => 'sometimes|string|max:20',
        'street' => 'sometimes|string',
        'country' => 'sometimes|string',
        'city' => 'sometimes|string',
        'is_default' => 'sometimes|boolean',
     ]);

     if($address->user_id !== auth()->id()){

     return response()->json([
        'status' => false,
        'message' => 'Unauthorized',

     ],403);
     }


        if($request->is_default){
            Address::where('user_id',auth()->id())
                                ->update(['is_default'=>false]);
        }


        $address->update($request->only([
            'name',
            'phone',
            'street',
            'country',
            'city',
            'is_default'
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Address updated successfully',
            'data' => $address
        ]);

     }

     public function destroy(Address $address){

      if($address->user_id !== auth()->id()){

     return response()->json([
        'status' => false,
        'message' => 'Unauthorized',

     ],403);
     }

     $address->delete();

     return response()->json([
        'status' => true,
        'message' => 'Address deleted successfully',
     ]);


}
}
