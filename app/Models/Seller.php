<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Seller extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'f_name',
        'l_name',
        'phone',
        'image',
        'email',
        'status',
        'bank_name',
        'branch',
        'account_no',
        'holder_name',
        'sales_commission_percentage',
        'gst',
        'balance',

    ];

    protected $hidden = [
        'password', 'remember_token', 'temporary_token', 'cm_firebase_token'
    ];

    protected $casts = [
        'id' => 'integer',
        'orders_count' => 'integer',
        'product_count' => 'integer',
    ];

    public function scopeApproved($query)
    {
        return $query->where(['status' => 'approved']);
    }

    public function shop()
    {
        return $this->hasOne(Shop::class, 'seller_id');
    }

    public function shops()
    {
        return $this->hasMany(Shop::class, 'seller_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function product()
    {
        return $this->hasMany(Product::class, 'user_id')->where(['added_by' => 'seller']);
    }

    public function wallet()
    {
        return $this->hasOne(SellerWallet::class);
    }

    public function fullname(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucfirst($this->f_name) . " " . ucfirst($this->l_name),
        );
    }

    public function product_manager()
    {
        return $this->belongsTo(Seller::class);
    }
}
