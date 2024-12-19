<?php

namespace App\Models;

use App\Services\AdditionalServices;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerVisitor extends Model
{
    use HasFactory;
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    protected $fillable = ["customer_id","visitor_id"];
}
