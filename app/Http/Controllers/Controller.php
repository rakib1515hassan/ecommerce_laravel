<?php

namespace App\Http\Controllers;

use App\Services\AdditionalServices;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        try {
            AdditionalServices::currency_load();
        } catch (\Exception $exception) {
        }
    }

    public function successResponse($message = "Api successfully worked", $data = [], $status_code = 200)
    {
        return \response()->json([
            'success' => true,
            'error'   => false,
            'message' => $message,
            'data'  => $data
        ], $status_code);
    }

    public function error($message = "Api successfully worked", $data = [], $status_code = 400)
    {
        return \response()->json([
            'success' => false,
            'error'   => true,
            'message' => $message,
            'errors'  => $data
        ], $status_code);
    }
}
