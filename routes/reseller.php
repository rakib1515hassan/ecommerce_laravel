<?php

use App\Http\Controllers\Reseller\DashboardController;
use Illuminate\Support\Facades\Route;


// Re-Seller
Route::group(['namespace' => 'Reseller', 'prefix' => 'reseller', 'as' => 'reseller.'], function () {

    /*authentication*/
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        // Register
        Route::get('apply', 'RegisterController@create')->name('apply');
        Route::post('apply', 'RegisterController@store');
        // Route::match(['get', 'post'], 'apply/{sellerId}/{productManagerId}', 'RegisterController@store');

        // Login
        Route::get('login', 'LoginController@login')->name('login');
        Route::post('login', 'LoginController@submit');
        Route::get('logout', 'LoginController@logout')->name('logout');

        // Logout
        Route::get('forgot-password', 'ForgotPasswordController@forgot_password')->name('forgot-password');
        Route::post('forgot-password', 'ForgotPasswordController@reset_password_request');
        Route::get('reset-password', 'ForgotPasswordController@reset_password_index')->name('reset-password');
        Route::post('reset-password', 'ForgotPasswordController@reset_password_submit');
    });

    /*Features authenticated*/
    Route::group(['middleware' => [\App\Http\Middleware\ResellerMiddleware::class]], function () {
        //dashboard routes
        Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
            Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('index');
            Route::get('/', [DashboardController::class, 'dashboard'])->name('index');
        });

        Route::group(['prefix' => 'sale-product', 'as' => 'sale-product.'], function () {
            Route::get('', 'SaleProductController@create')->name('index');
            Route::post('', 'SaleProductController@store')->name('store');

            Route::get('search-customer', 'SaleProductController@search_customer')->name('search-customer');
            Route::post('create-customer', 'SaleProductController@create_customer')->name('create-customer');
            Route::get('get-shipping-address', 'SaleProductController@get_shipping_address')->name('get-shipping-address');
            Route::get('get-area', 'SaleProductController@get_area')->name('get-area');
            Route::post('create-shipping-address', 'SaleProductController@create_shipping_address')->name('create-shipping-address');
            Route::get('search-product', 'SaleProductController@search_product')->name('search-product');
            Route::post('create-order', 'SaleProductController@create_order')->name('create-order');
            Route::post('get-shipping-cost', 'SaleProductController@shipping_cost')->name('shipping-cost');
        });
//        // Sell Product
//        Route::resource('sale-product', SaleProductController::class)->only(['index', 'create', 'store']);

        //Product
        Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
            Route::post('image-upload', 'ProductController@imageUpload')->name('image-upload');
            Route::get('remove-image', 'ProductController@remove_image')->name('remove-image');
            Route::get('add-new', 'ProductController@add_new')->name('add-new');
            Route::post('add-new', 'ProductController@store');
            Route::post('status-update', 'ProductController@status_update')->name('status-update');
            Route::get('list', 'ProductController@list')->name('list');
            Route::get('stock-limit-list/{type}', 'ProductController@stock_limit_list')->name('stock-limit-list');
            Route::get('get-variations', 'ProductController@get_variations')->name('get-variations');
            Route::post('update-quantity', 'ProductController@update_quantity')->name('update-quantity');
            Route::get('edit/{id}', 'ProductController@edit')->name('edit');
            Route::post('update/{id}', 'ProductController@update')->name('update');
            Route::post('sku-combination', 'ProductController@sku_combination')->name('sku-combination');
            Route::get('get-categories', 'ProductController@get_categories')->name('get-categories');
            Route::delete('delete/{id}', 'ProductController@delete')->name('delete');
            Route::get('view/{id}', 'ProductController@view')->name('view');
            Route::get('bulk-import', 'ProductController@bulk_import_index')->name('bulk-import');
            Route::post('bulk-import', 'ProductController@bulk_import_data');
            Route::get('bulk-export', 'ProductController@bulk_export_data')->name('bulk-export');
        });

        // profile
        Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
            Route::get('view', 'ProfileController@view')->name('view');
            Route::get('update/{id}', 'ProfileController@edit')->name('update');
            Route::post('update/{id}', 'ProfileController@update');
            Route::post('settings-password', 'ProfileController@settings_password_update')->name('settings-password');
            Route::get('bank-edit/{id}', 'ProfileController@bank_edit')->name('bankInfo');
            Route::post('bank-update/{id}', 'ProfileController@bank_update')->name('bank_update');
        });

        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
            Route::get('list/{status}', 'OrderController@list')->name('list');
            Route::get('details/{id}', 'OrderController@details')->name('details');
            Route::get('generate-invoice/{id}', 'OrderController@generate_invoice')->name('generate-invoice');
            Route::post('status', 'OrderController@status')->name('status');
            Route::post('productStatus', 'OrderController@productStatus')->name('productStatus');
            Route::post('payment-status', 'OrderController@payment_status')->name('payment-status');
            Route::get('add-product_manager/{order_id}/{d_man_id}', 'OrderController@add_delivery_man')->name('add-delivery-man');
        });

        // Withdraw
        Route::group(['prefix' => 'withdraw', 'as' => 'withdraw.'], function () {
            Route::post('request', 'WithdrawController@w_request')->name('request');
            Route::delete('close/{id}', 'WithdrawController@close_request')->name('close');
        });

        // Business Section
        Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.'], function () {

            Route::group(['prefix' => 'shipping-method', 'as' => 'shipping-method.'], function () {
                Route::get('add', 'ShippingMethodController@index')->name('add');
                Route::post('add', 'ShippingMethodController@store');
                Route::get('edit/{id}', 'ShippingMethodController@edit')->name('edit');
                Route::put('update/{id}', 'ShippingMethodController@update')->name('update');
                Route::post('delete', 'ShippingMethodController@delete')->name('delete');
                Route::post('status-update', 'ShippingMethodController@status_update')->name('status-update');
            });

            Route::group(['prefix' => 'withdraw', 'as' => 'withdraw.'], function () {
                Route::get('list', 'WithdrawController@list')->name('list');
                Route::get('cancel/{id}', 'WithdrawController@close_request')->name('cancel');
                Route::post('status-filter', 'WithdrawController@status_filter')->name('status-filter');
            });
        });
    });
});
