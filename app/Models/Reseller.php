<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Reseller extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasApiTokens;

    protected $hidden = [
        'password',
        'auth_token',
        'remember_token',
        'seller_id',
        'reseller_id',
        'product_id',
        'new_slug',
        'new_price'
    ];

    public function scopeApproved($query)
    {
        return $query->where(['is_active' => '1']);
    }


    public function fullname(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ucfirst($this->f_name) . " " . ucfirst($this->l_name),
        );
    }
}
