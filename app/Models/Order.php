<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'address_id',
        'status',
        'sub_total',
        'discount',
        'shipping_cost',
        'total',
        'payment_method',
        'payment_status'

    ];
            const STATUS_PENDING    = 'pending';
            const STATUS_CONFIRMED  = 'confirmed';
            const STATUS_PROCESSING = 'processing';
            const STATUS_SHIPPED    = 'shipped';
            const STATUS_DELIVERED  = 'delivered';
            const STATUS_CANCELLED  = 'cancelled';

    public function orderItems(){

    return $this-> hasMany(OrderItem::class);
    }

    public function address(){

    return $this->belongsTo(Address::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

}
