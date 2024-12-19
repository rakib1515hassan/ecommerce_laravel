<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashDealProduct extends Model
{
    protected $fillable = [
        'seller_id',
        'flash_deal_id',
        'product_id',
        'discount',
        'discount_type',
        'status',
        'seller_is'
    ];

    protected $casts = [
        'product_id' => 'integer',
        'discount' => 'float',
        'flash_deal_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
