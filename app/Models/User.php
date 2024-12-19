<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'f_name', 'l_name', 'name', 'email', 'password', 'phone', 'image', 'login_medium', 'is_active', 'social_id', 'is_phone_verified', 'temporary_token', 'is_membership'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'temporary_token', 'cm_firebase_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function wish_list()
    {
        return $this->hasMany(Wishlist::class, 'customer_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function shipping()
    {
        return $this->belongsTo(ShippingAddress::class, 'shipping_address_id');
    }

    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class, 'created_by_id', 'id');
    }
    public function customerPosts()
    {
        return $this->hasMany(Posts::class, 'created_by_id', 'id');
    }

    public function fullname(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ucfirst($this->f_name) . " " . ucfirst($this->l_name),
        );
    }

    public function address()
    {
        return $this->hasMany(ShippingAddress::class, 'customer_id');
    }

    // Membership

    public function membership()
    {
        return $this->hasOne(Membership::class);
    }

    public function referrals()
    {
        return $this->hasMany(Membership::class, 'referred_from');
    }

    public function customerfullname(): string
    {
        return ucfirst($this->f_name) . " " . ucfirst($this->l_name);
    }

    public function pointHistories()
    {
        return $this->hasMany(PointHistory::class, 'user_id');
    }

    public function referredPointHistories()
    {
        return $this->hasMany(PointHistory::class, 'referred_user');
    }

    // User all recommended products keywords
    public function recommendedProducts()
    {
        return $this->hasMany(RecommendedProduct::class);
    }
}
