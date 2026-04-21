<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use App\Models\Cart;
// use App\Models\Product;
// use App\Models\ProductVariant;


class CartItem extends Model
{
    protected $fillable = [
        'product_id',
        'cart_id',
        'variant_id',
        'quantity',
        'price',
    ];

    public function cart(){

    return $this->belongsTo(Cart::class);

    }

    public function product(){

        return $this->belongsTo(Product::class);

    }

    public function variant(){

     return $this->belongsTo(ProductVariant::class);
    }






}
