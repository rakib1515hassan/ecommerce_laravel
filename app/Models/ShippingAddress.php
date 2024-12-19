<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    protected $guarded = [];

    protected $casts = [
        'customer_id' => 'integer',
    ];

    public function area()
    {
        return $this->belongsTo(AddressArea::class, 'area_id');
    }
}
