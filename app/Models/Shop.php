<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $casts = [
        'seller_id ' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function area()
    {
        return $this->belongsTo(AddressArea::class, 'area_id');
    }

    public function scopeActive($query)
    {
        return $query->whereHas('seller', function ($query) {
            $query->where(['status' => 'approved']);
        });
    }
}
