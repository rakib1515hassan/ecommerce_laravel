<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'auth'], function () {
    Route::post('login', 'LoginController@login');
    Route::post('logout', 'LoginController@logout')->middleware('auth:api-seller');
    Route::post('register', 'RegisterController@register');
});


Route::group(['middleware' => ['api_lang', 'auth:api-seller']], function () {
    Route::get('seller-info', 'SellerController@seller_info');
    Route::put('seller-update', 'SellerController@seller_info_update');
    Route::get('seller-delivery-man', 'SellerController@seller_delivery_man');
    Route::get('order-product-reviews', 'SellerController@shop_product_reviews');
    Route::get('monthly-earning', 'SellerController@monthly_earning');
    Route::get('monthly-commission-given', 'SellerController@monthly_commission_given');
    Route::put('cm-firebase-token', 'SellerController@update_cm_firebase_token');

    Route::get('order-info', 'SellerController@shop_info');
    Route::get('transactions', 'SellerController@transaction');
    Route::put('order-update', 'SellerController@shop_info_update');

    Route::post('balance-withdraw', 'SellerController@withdraw_request');
    Route::delete('close-withdraw-request', 'SellerController@close_withdraw_request');

    Route::group(['prefix' => 'products'], function () {
        Route::post('upload-images', 'ProductController@upload_images');
        Route::get('list', 'ProductController@list');
        Route::post('add', 'ProductController@add_new');
        Route::get('stock-out-list', 'ProductController@stock_out_list');
        Route::get('edit/{id}', 'ProductController@edit');
        Route::put('update/{id}', 'ProductController@update');
        Route::delete('delete/{id}', 'ProductController@delete');
    });

    Route::group(['prefix' => 'orders'], function () {
        Route::get('list', 'OrderController@list');
        Route::get('/{id}', 'OrderController@details');
        Route::put('order-detail-status/{id}', 'OrderController@order_detail_status');
        Route::put('assign-delivery-man', 'OrderController@assign_delivery_man');
    });

    Route::group(['prefix' => 'shipping-method'], function () {
        Route::get('list', 'ShippingMethodController@list');
        Route::post('add', 'ShippingMethodController@store');
        Route::get('edit/{id}', 'ShippingMethodController@edit');
        Route::put('status', 'ShippingMethodController@status_update');
        Route::put('update/{id}', 'ShippingMethodController@update');
        Route::delete('delete/{id}', 'ShippingMethodController@delete');
    });

    Route::group(['prefix' => 'messages'], function () {
        Route::get('list', 'ChatController@messages');
        Route::post('send', 'ChatController@send_message');
    });

    // Start::Deals Api (Sajib)
    Route::post('flash-deals/{flash_id}/add-product', 'DealsController@addProductToFlashDeal')->whereNumber('flash_id');
    Route::post('feature-deals/{feature_id}/add-product', 'DealsController@addProductToFeatureDeal')->whereNumber('feature_id');
    // End::Deals Api (Sajib)
});

// Route::post('ls-lib-update', 'LsLibController@lib_update');
