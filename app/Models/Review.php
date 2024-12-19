<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $casts = [
        'product_id'  => 'integer',
        'customer_id' => 'integer',
        'rating'      => 'integer',
        'status'      => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'customer_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
