<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'coupon_type', 'title', 'code', 'start_date', 'expire_date',
        'min_purchase', 'max_discount', 'discount', 'discount_type',
        'status', 'limit', 'user_id',
    ];

    protected $casts = [
        'min_purchase' => 'float',
        'max_discount' => 'float',
        'discount'     => 'float',
        'status'       => 'integer',
        'start_date'   => 'date',
        'expire_date'  => 'date',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
