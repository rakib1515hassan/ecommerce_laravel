<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerWallet extends Model
{
    protected $fillable = [
        'seller_id',
        'total_earning',
        'withdrawn',
        'commission_given',
        'pending_withdraw',
        'delivery_charge_earned',
        'collected_cash',
        'total_tax_collected',

    ];


    protected $casts = [
        'total_earning' => 'float',
        'withdrawn' => 'float',
        'commission_given' => 'float',
        'pending_withdraw' => 'float',
        'delivery_charge_earned' => 'float',
        'collected_cash' => 'float',
        'total_tax_collected' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
