<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerWallet extends Model
{
    protected $fillable = [
        'reseller_id',
        'total_earning',
        'withdrawn',
        'pending_withdraw',
    ];


    use HasFactory;
}
