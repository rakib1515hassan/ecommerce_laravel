<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Admin extends Authenticatable
{
    use Notifiable;

    // protected $fillable = ['f_name', 'l_name'];

    public function role()
    {
        return $this->belongsTo(AdminRole::class, 'admin_role_id');
    }


    protected $hidden = [
        'password', 'remember_token', 'email_verified_at'
    ];


    public function fullname(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => \ucfirst($this->name),
        );
    }
}
