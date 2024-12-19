<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionAnswering extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'customer_id',
        'question',
        'answer',
        'answered_by',
        'status',
        'awswered_by_admin'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }

    public function answeredBySeller()
    {
        return $this->belongsTo(Seller::class, 'answered_by', 'id');
    }

    public function answeredByAdmin()
    {
        return $this->belongsTo(Admin::class, 'answered_by', 'id');
    }
}
