<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //

    use HasFactory;
        protected $fillable = [
            'category_id',
            'name',
            'slug',
            'description',
            'price',
            'sale_price',
            'brand',
            'stock',
            'status',
            'is_featured',
            'is_new_arrival'
            ];

            protected $casts = [
    'price'          => 'decimal:2',
    'sale_price'     => 'decimal:2',
    'stock'          => 'integer',
    'status'         => 'boolean',
    'is_featured'    => 'boolean',
    'is_new_arrival' => 'boolean',
];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }


     public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

     public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

     public function getRouteKeyName(): string
    {
        return 'slug';
    }

}
