<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $casts = [
        'order_amount' => 'float',
        'discount_amount' => 'float',
        'customer_id' => 'integer',
        'shipping_address_id' => 'integer',
        'shipping_cost' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_shipped' => 'boolean',
    ];

    protected $fillable = [
        'customer_id',
        'customer_type',
        'payment_status',
        'order_status',
        'payment_method',
        'transaction_ref',
        'order_amount',
        'shipping_address_id',
        'discount_amount',
        'discount_type',
        'coupon_code',
        'shipping_method_id',
        'shipping_cost',
        'is_shipped',
        'order_group_id',
        'verification_code',
        'seller_id',
        'seller_is',
        'reseller_id',
        'shipping_address_data',
        'delivery_man_id',
        'order_note',
        'billing_address',
        'billing_address_data',
        'is_delivered',
        'is_paid',
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class)->orderBy('seller_id', 'ASC');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function sellerName()
    {
        return $this->hasOne(OrderDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function shipping()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }

    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'shipping_address_id');
    }

    public function billingAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'billing_address');
    }

    public function delivery_man()
    {
        return $this->belongsTo(DeliveryMan::class, 'delivery_man_id');
    }
}
