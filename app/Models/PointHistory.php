<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'referred_user',
        'points',
        'order_id',
        'order_amount',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

}
