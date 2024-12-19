<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductManagerWallet extends Model
{
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
