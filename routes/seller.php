<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

use App\Http\Controllers\Seller\AcceptRegistrationController;
use App\Http\Controllers\Seller\DealController;
use App\Http\Controllers\Seller\FeatureDealController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Seller', 'prefix' => 'seller', 'as' => 'seller.'], function () {
    Route::group(['prefix' => 'order', 'as' => 'order.', 'namespace' => 'Auth'], function () {
        Route::get('apply', 'RegisterController@create')->name('apply');
        Route::post('apply', 'RegisterController@store');
    });

    /*authentication*/
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::get('login', 'LoginController@login')->name('login');
        Route::post('login', 'LoginController@submit');
        Route::get('logout', 'LoginController@logout')->name('logout');

        Route::get('forgot-password', 'ForgotPasswordController@forgot_password')->name('forgot-password');
        Route::post('forgot-password', 'ForgotPasswordController@reset_password_request');
        Route::get('reset-password', 'ForgotPasswordController@reset_password_index')->name('reset-password');
        Route::post('reset-password', 'ForgotPasswordController@reset_password_submit');
    });

    /*authenticated*/
    Route::group(['middleware' => ['seller']], function () {
        //dashboard routes
        Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
            Route::get('dashboard', 'DashboardController@dashboard');
            Route::get('/', 'DashboardController@dashboard')->name('index');
            Route::post('order-stats', 'DashboardController@order_stats')->name('order-stats');
            Route::post('business-overview', 'DashboardController@business_overview')->name('business-overview');
        });

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

        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
            Route::get('list/{status}', 'OrderController@list')->name('list');
            Route::get('details/{id}', 'OrderController@details')->name('details');
            Route::get('generate-invoice/{id}', 'OrderController@generate_invoice')->name('generate-invoice');
            Route::post('status', 'OrderController@status')->name('status');
            Route::post('productStatus', 'OrderController@productStatus')->name('productStatus');
            Route::post('payment-status', 'OrderController@payment_status')->name('payment-status');

            Route::get('add-product_manager/{order_id}/{d_man_id}', 'OrderController@add_delivery_man')->name('add-delivery-man');
        });

        //Product Reviews
        Route::group(['prefix' => 'reviews', 'as' => 'reviews.'], function () {
            Route::get('list', 'ReviewsController@list')->name('list');
        });

        // Messaging
        Route::group(['prefix' => 'messages', 'as' => 'messages.'], function () {
            Route::get('/chat', 'ChattingController@chat')->name('chat');
            Route::get('/message-by-user', 'ChattingController@message_by_user')->name('message_by_user');
            Route::post('/seller-message-store', 'ChattingController@seller_message_store')->name('seller_message_store');
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


        Route::group(['prefix' => 'order', 'as' => 'order.'], function () {
            Route::get('view', 'ShopController@view')->name('view');
            Route::get('edit/{id}', 'ShopController@edit')->name('edit');
            Route::post('update/{id}', 'ShopController@update')->name('update');
        });

        Route::group(['prefix' => 'withdraw', 'as' => 'withdraw.'], function () {
            Route::post('request', 'WithdrawController@w_request')->name('request');
            Route::delete('close/{id}', 'WithdrawController@close_request')->name('close');
        });

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

            Route::group(['prefix' => 'redx', 'as' => 'redx.'], function () {
                Route::get('/', 'RedXController@profile')->name('profile');
                Route::post('profile-save', 'RedXController@profileSave')->name('profile.save');
            });

            Route::group(['prefix' => 'qa', 'as' => 'qa.'], function () {
                Route::get('/', 'QAController@index')->name('index');
                Route::get('/{id}/show', 'QAController@show')->name('show');
                Route::get('/{id}/reply', 'QAController@reply')->name('reply');
            });
        });

        // Delivery Man
        Route::group(['prefix' => 'delivery-man', 'as' => 'delivery-man.'], function () {
            Route::get('add', 'DeliveryManController@index')->name('add');
            Route::post('store', 'DeliveryManController@store')->name('store');
            Route::get('list', 'DeliveryManController@list')->name('list');
            Route::get('preview/{id}', 'DeliveryManController@preview')->name('preview');
            Route::get('edit/{id}', 'DeliveryManController@edit')->name('edit');
            Route::post('update/{id}', 'DeliveryManController@update')->name('update');
            Route::delete('delete/{id}', 'DeliveryManController@delete')->name('delete');
            Route::post('search', 'DeliveryManController@search')->name('search');
            Route::post('status-update', 'DeliveryManController@status')->name('status-update');
        });


        // Product Manager
        Route::group(['prefix' => 'product_manager', 'as' => 'product_manager.'], function () {
            Route::get('add', 'ProductManagerController@index')->name('add');
            Route::post('store', 'ProductManagerController@store')->name('store');
            Route::get('list', 'ProductManagerController@list')->name('list');
            Route::get('preview/{id}', 'ProductManagerController@preview')->name('preview');
            Route::get('edit/{id}', 'ProductManagerController@edit')->name('edit');
            Route::post('update/{id}', 'ProductManagerController@update')->name('update');
            Route::delete('delete/{id}', 'ProductManagerController@delete')->name('delete');
            Route::post('search', 'ProductManagerController@search')->name('search');
            Route::post('status-update', 'ProductManagerController@status')->name('status-update');
        });

        // Reseller
        Route::group(['prefix' => 'reseller', 'as' => 'reseller.'], function () {
            Route::get('add', 'ResellerController@index')->name('add');
            Route::post('store', 'ResellerController@store')->name('store');
            Route::get('list', 'ResellerController@list')->name('list');
            Route::get('preview/{id}', 'ResellerController@preview')->name('preview');
            Route::get('edit/{id}', 'ResellerController@edit')->name('edit');
            Route::post('update/{id}', 'ResellerController@update')->name('update');
            Route::delete('delete/{id}', 'ResellerController@delete')->name('delete');
            Route::post('search', 'ResellerController@search')->name('search');
            Route::post('status-update', 'ResellerController@status')->name('status-update');
        });

        Route::group(['prefix' => 'deal', 'as' => 'deal.'], function () {
            Route::get('flash', [DealController::class, 'flash_index'])->name('flash');

            //product
            Route::get('add-product/{deal_id}', [DealController::class, 'add_product'])->name('add-product');
            Route::post('add-product/{deal_id}/discount-update', [DealController::class, 'discount_update'])->name('discount-update');
            Route::post('add-product/{deal_id}', [DealController::class, 'add_product_submit']);
            Route::post('product-status-update', [DealController::class, 'product_status_update'])->name('product-status-update');
            Route::get('delete-product/{deal_product_id}', [DealController::class, 'delete_product'])->name('delete-product');

            Route::group(['prefix' => 'feature', 'as' => 'feature.', 'controller' => FeatureDealController::class], function () {
                Route::get('/', 'featureIndex')->name('index');
                Route::get('add-product/{deal_id}', 'addProduct')->name('add-product');
                Route::post('add-product/{deal_id}', 'addProductSubmit');
                Route::get('delete-product/{deal_product_id}', 'deleteProduct')->name('delete-product');
            });
        });

        // Accept Registration
        Route::group(['prefix' => 'product_manager', 'as' => 'product_manager.', 'middleware' => ['module:user_section']], function () {
            Route::get('list', [AcceptRegistrationController::class, 'product_manager_list'])->name('list');
            Route::post('update-status', [AcceptRegistrationController::class, 'updateStatus'])->name('updateStatus');
        });
    });
});
