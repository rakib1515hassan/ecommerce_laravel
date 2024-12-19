<?php

use App\Http\Controllers\SslCommerzPaymentController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

//sellerShop
Route::get('shopView/{id}', [WebController::class, 'seller_shop'])->name('shopView');
Route::post('shopView/{id}', [WebController::class, 'seller_shop_product']);


Route::group(['prefix' => 'sslcommerz', 'as' => 'sslcommerz.'], function () {
    Route::post('success', [SslCommerzPaymentController::class, 'success'])->name('success');
    Route::post('fail', [SslCommerzPaymentController::class, 'fail'])->name('failed');
    Route::post('cancel', [SslCommerzPaymentController::class, 'cancel'])->name('cancel');
    Route::post('ipn', [SslCommerzPaymentController::class, 'success'])->name('ipn');
});
