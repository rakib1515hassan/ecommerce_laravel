<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'referral_id',
        'referred_from',
        'points',
        'status',
        'verification_type',
        'verification_id',
    ];

    /**
     * Get the user that owns the membership.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_from');
    }
}
