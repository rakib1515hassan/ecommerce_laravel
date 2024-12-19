<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawRequest extends Model
{

    protected $fillable = [
        'person',
        'person_id',
        'admin_id',
        'amount',
        'transaction_note',
        'approved',
    ];

    protected $casts = [
        'amount' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        if ($this->person == 'reseller') {
            return $this->BelongsTo(Reseller::class, 'person_id');
        } elseif ($this->person == 'product_manager') {
            return $this->BelongsTo(ProductManager::class, 'person_id');
        } else {
            return $this->BelongsTo(Seller::class, 'person_id');
        }
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'person_id');
    }

    public function reseller()
    {
        return $this->belongsTo(Reseller::class, 'person_id');
    }

    public function product_manager()
    {
        return $this->belongsTo(ProductManager::class, 'person_id');
    }

}
