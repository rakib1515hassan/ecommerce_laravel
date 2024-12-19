<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class ProductManager extends Authenticatable implements MustVerifyEmail
{
    protected $hidden = ['password', 'auth_token', 'remember_token'];
    use Notifiable, HasApiTokens;

    public function scopeApproved($query)
    {
        return $query->where(['is_active' => '1']);
    }

    public function product()
    {
        return $this->hasMany(Product::class, 'user_id')->where(['added_by' => 'seller']);
    }

    public function fullname(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ucfirst($this->f_name) . " " . ucfirst($this->l_name),
        );
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }


    // public function seller()
    // {
    //     return $this->belongsTo(ProductManager::class, 'seller_id');
    // }

    // public function product_manager()
    // {
    //     return $this->belongsTo(ProductManager::class);
    // }
}
