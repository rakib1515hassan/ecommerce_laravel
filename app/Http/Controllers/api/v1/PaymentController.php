<?php

namespace App\Http\Controllers\api\v1;

use App\Services\PaymentService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        return response()->json(PaymentService::all_payment_methods());
    }
}
