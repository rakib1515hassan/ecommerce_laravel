<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class OrderTransaction extends Model
{
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
