<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerProduct extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'seller_id', 'reseller_id', 'product_id', 'new_slug', 'new_price'];
}
