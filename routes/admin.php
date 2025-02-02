<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Admin', 'prefix' => '/', 'as' => 'admin.'], function () {

    Route::get('/', function () {
        return redirect()->route('admin.auth.login');
    });

    /*authentication*/
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::get('login', 'LoginController@login')->name('login');
        Route::post('login', 'LoginController@submit')->middleware('actch');
        Route::get('logout', 'LoginController@logout')->name('logout');
    });

    /*authenticated*/
    Route::group(['middleware' => ['admin']], function () {
        //dashboard routes
        Route::get('/', 'DashboardController@dashboard')->name('dashboard'); //previous dashboard route
        Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
            Route::get('/', 'DashboardController@dashboard')->name('index');
            Route::post('order-stats', 'DashboardController@order_stats')->name('order-stats');
            Route::post('business-overview', 'DashboardController@business_overview')->name('business-overview');
        });

        //system routes
        Route::get('search-function', 'SystemController@search_function')->name('search-function');
        Route::get('maintenance-mode', 'SystemController@maintenance_mode')->name('maintenance-mode');

        Route::group(['prefix' => 'custom-role', 'as' => 'custom-role.', 'middleware' => ['module:employee_section']], function () {
            Route::get('create', 'CustomRoleController@create')->name('create');
            Route::post('create', 'CustomRoleController@store');
            Route::get('update/{id}', 'CustomRoleController@edit')->name('update');
            Route::post('update/{id}', 'CustomRoleController@update');
        });

        Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
            Route::get('view', 'ProfileController@view')->name('view');
            Route::get('update/{id}', 'ProfileController@edit')->name('update');
            Route::post('update/{id}', 'ProfileController@update');
            Route::post('settings-password', 'ProfileController@settings_password_update')->name('settings-password');
        });

        Route::group(['prefix' => 'withdraw', 'as' => 'withdraw.', 'middleware' => ['module:user_section']], function () {
            Route::get('list/', 'WithdrawController@index')->name('index');
            Route::post('update/', 'WithdrawController@StatusUpdate')->name('status_update');
            Route::post('request', 'WithdrawController@w_request')->name('request');
            Route::post('status-filter', 'WithdrawController@status_filter')->name('status-filter');
        });

        Route::group(['prefix' => 'deal', 'as' => 'deal.', 'middleware' => ['module:marketing_section']], function () {
            Route::get('flash', 'DealController@flash_index')->name('flash');
            Route::post('flash', 'DealController@flash_submit');

            // feature deal
            Route::get('feature', 'DealController@feature_index')->name('feature');

            Route::get('day', 'DealController@deal_of_day')->name('day');
            Route::post('day', 'DealController@deal_of_day_submit');
            Route::post('day-status-update', 'DealController@day_status_update')->name('day-status-update');

            Route::get('day-update/{id}', 'DealController@day_edit')->name('day-update');
            Route::post('day-update/{id}', 'DealController@day_update');
            Route::get('day-delete/{id}', 'DealController@day_delete')->name('day-delete');

            Route::get('update/{id}', 'DealController@edit')->name('update');
            Route::get('edit/{id}', 'DealController@feature_edit')->name('edit');

            Route::post('update/{id}', 'DealController@update')->name('update');
            Route::post('status-update', 'DealController@status_update')->name('status-update');
            Route::post('feature-status', 'DealController@feature_status')->name('feature-status');

            Route::post('featured-update', 'DealController@featured_update')->name('featured-update');
            Route::get('add-product/{deal_id}', 'DealController@add_product')->name('add-product');
            Route::post('add-product/{deal_id}/discount-update', 'DealController@discount_update')->name('discount-update');
            Route::post('add-product/{deal_id}', 'DealController@add_product_submit');
            Route::post('product-status-update', 'DealController@product_status_update')->name('product-status-update');
            Route::get('delete-product/{deal_product_id}', 'DealController@delete_product')->name('delete-product');
        });

        Route::group(['prefix' => 'employee', 'as' => 'employee.', 'middleware' => ['module:employee_section']], function () {
            Route::get('add-new', 'EmployeeController@add_new')->name('add-new');
            Route::post('add-new', 'EmployeeController@store');
            Route::get('list', 'EmployeeController@list')->name('list');
            Route::get('update/{id}', 'EmployeeController@edit')->name('update');
            Route::post('update/{id}', 'EmployeeController@update');
        });

        Route::group(['prefix' => 'category', 'as' => 'category.', 'middleware' => ['module:product_management']], function () {
            Route::get('view', 'CategoryController@index')->name('view');
            Route::get('fetch', 'CategoryController@fetch')->name('fetch');
            Route::post('store', 'CategoryController@store')->name('store');
            Route::get('edit/{id}', 'CategoryController@edit')->name('edit');
            Route::post('update/{id}', 'CategoryController@update')->name('update');
            Route::post('delete', 'CategoryController@delete')->name('delete');
            Route::get('status/{id}/{home_status}', 'CategoryController@status')->name('status');
        });

        Route::group(['prefix' => 'sub-category', 'as' => 'sub-category.', 'middleware' => ['module:product_management']], function () {
            Route::get('view', 'SubCategoryController@index')->name('view');
            Route::get('fetch', 'SubCategoryController@fetch')->name('fetch');
            Route::post('store', 'SubCategoryController@store')->name('store');
            Route::post('edit', 'SubCategoryController@edit')->name('edit');
            Route::post('update', 'SubCategoryController@update')->name('update');
            Route::post('delete', 'SubCategoryController@delete')->name('delete');
        });

        Route::group(['prefix' => 'sub-sub-category', 'as' => 'sub-sub-category.', 'middleware' => ['module:product_management']], function () {
            Route::get('view', 'SubSubCategoryController@index')->name('view');
            Route::get('fetch', 'SubSubCategoryController@fetch')->name('fetch');
            Route::post('store', 'SubSubCategoryController@store')->name('store');
            Route::post('edit', 'SubSubCategoryController@edit')->name('edit');
            Route::post('update', 'SubSubCategoryController@update')->name('update');
            Route::post('delete', 'SubSubCategoryController@delete')->name('delete');
            Route::post('get-sub-category', 'SubSubCategoryController@getSubCategory')->name('getSubCategory');
            Route::post('get-category-id', 'SubSubCategoryController@getCategoryId')->name('getCategoryId');
        });

        Route::group(['prefix' => 'brand', 'as' => 'brand.', 'middleware' => ['module:product_management']], function () {
            Route::get('add-new', 'BrandController@add_new')->name('add-new');
            Route::post('add-new', 'BrandController@store');
            Route::get('list', 'BrandController@list')->name('list');
            Route::get('update/{id}', 'BrandController@edit')->name('update');
            Route::post('update/{id}', 'BrandController@update');
            Route::post('delete', 'BrandController@delete')->name('delete');
        });

        Route::group(['prefix' => 'banner', 'as' => 'banner.', 'middleware' => ['module:marketing_section']], function () {
            Route::post('add-new', 'BannerController@store')->name('store');
            Route::get('list', 'BannerController@list')->name('list');
            Route::post('delete', 'BannerController@delete')->name('delete');
            Route::post('status', 'BannerController@status')->name('status');
            Route::get('edit/{id}', 'BannerController@edit')->name('edit');
            Route::put('update/{id}', 'BannerController@update')->name('update');
        });

        Route::group(['prefix' => 'attribute', 'as' => 'attribute.', 'middleware' => ['module:product_management']], function () {
            Route::get('view', 'AttributeController@index')->name('view');
            Route::get('fetch', 'AttributeController@fetch')->name('fetch');
            Route::post('store', 'AttributeController@store')->name('store');
            Route::get('edit/{id}', 'AttributeController@edit')->name('edit');
            Route::post('update/{id}', 'AttributeController@update')->name('update');
            Route::post('delete', 'AttributeController@delete')->name('delete');
        });

        Route::group(['prefix' => 'coupon', 'as' => 'coupon.', 'middleware' => ['module:marketing_section']], function () {
            Route::get('add-new', 'CouponController@add_new')->name('add-new')->middleware('actch');;
            Route::post('add-new', 'CouponController@store');
            Route::get('update/{id}', 'CouponController@edit')->name('update')->middleware('actch');;
            Route::post('update/{id}', 'CouponController@update');
            Route::get('status/{id}/{status}', 'CouponController@status')->name('status');
        });
        Route::group(['prefix' => 'social-login', 'as' => 'social-login.', 'middleware' => ['module:business_settings']], function () {
            Route::get('view', 'BusinessSettingsController@viewSocialLogin')->name('view');
            Route::post('update/{service}', 'BusinessSettingsController@updateSocialLogin')->name('update');
        });

        Route::group(['prefix' => 'currency', 'as' => 'currency.', 'middleware' => ['module:business_settings']], function () {
            Route::get('view', 'CurrencyController@index')->name('view')->middleware('actch');;
            Route::get('fetch', 'CurrencyController@fetch')->name('fetch');
            Route::post('store', 'CurrencyController@store')->name('store');
            Route::get('edit/{id}', 'CurrencyController@edit')->name('edit');
            Route::post('update/{id}', 'CurrencyController@update')->name('update');
            Route::get('delete/{id}', 'CurrencyController@delete')->name('delete');
            Route::post('status', 'CurrencyController@status')->name('status');
            Route::post('system-currency-update', 'CurrencyController@systemCurrencyUpdate')->name('system-currency-update');
        });

        Route::group(['prefix' => 'support-ticket', 'as' => 'support-ticket.', 'middleware' => ['module:support_section']], function () {
            Route::get('view', 'SupportTicketController@index')->name('view');
            Route::post('status', 'SupportTicketController@status')->name('status');
            Route::get('single-ticket/{id}', 'SupportTicketController@single_ticket')->name('singleTicket');
            Route::post('single-ticket/{id}', 'SupportTicketController@replay_submit')->name('replay');
        });
        Route::group(['prefix' => 'notification', 'as' => 'notification.', 'middleware' => ['module:marketing_section']], function () {
            Route::get('add-new', 'NotificationController@index')->name('add-new');
            Route::post('store', 'NotificationController@store')->name('store');
            Route::get('edit/{id}', 'NotificationController@edit')->name('edit');
            Route::post('update/{id}', 'NotificationController@update')->name('update');
            Route::post('status', 'NotificationController@status')->name('status');
            Route::post('delete', 'NotificationController@delete')->name('delete');
        });
        Route::group(['prefix' => 'reviews', 'as' => 'reviews.', 'middleware' => ['module:business_section']], function () {
            Route::get('list', 'ReviewsController@list')->name('list')->middleware('actch');
            Route::delete('review-delete/{id}', 'ReviewsController@review_delete')->name('review_delete');
        });

        Route::group(['prefix' => 'qa', 'as' => 'qa.', 'middleware' => ['module:business_section']], function () {
            Route::get('/', 'QAController@index')->name('index');
            Route::get('/{id}/show', 'QAController@show')->name('show');
            Route::get('/{id}/reply', 'QAController@reply')->name('reply');
        });

        Route::group(['prefix' => 'redx', 'as' => 'redx.', 'middleware' => ['module:business_section']], function () {
            Route::get('/', 'RedXController@profile')->name('profile');
            Route::post('profile-save', 'RedXController@profileSave')->name('profile.save');
        });

        Route::group(['prefix' => 'customer', 'as' => 'customer.', 'middleware' => ['module:user_section']], function () {
            Route::get('list', 'CustomerController@customer_list')->name('list');
            Route::post('status-update', 'CustomerController@status_update')->name('status-update');
            Route::get('view/{user_id}', 'CustomerController@view')->name('view');
            Route::delete('delete/{id}', 'CustomerController@delete')->name('delete');
        });

        // Membership Route
        Route::group(['prefix' => 'membership', 'as' => 'membership.', 'middleware' => ['module:user_section']], function () {
            Route::get('list', 'MembershipController@index')->name('list');
            Route::get('point/list/{id}', 'MembershipController@point_history')->name('point_history');
            Route::get('view/{user_id}', 'MembershipController@show')->name('show');
            Route::put('update/{id}', 'MembershipController@update')->name('update');
            Route::delete('delete/{id}', 'MembershipController@delete')->name('delete');
        });
        
        ///Report
        Route::group(['prefix' => 'report', 'as' => 'report.', 'middleware' => ['module:report']], function () {
            Route::get('order', 'ReportController@order_index')->name('order');
            Route::get('earning', 'ReportController@earning_index')->name('earning');
            Route::post('set-date', 'ReportController@set_date')->name('set-date');
            //sale report inhouse
            Route::get('inhoue-product-sale', 'InhouseProductSaleController@index')->name('inhoue-product-sale');
            Route::get('seller-product-sale', 'SellerProductSaleReportController@index')->name('seller-product-sale');
        });

        Route::group(['prefix' => 'stock', 'as' => 'stock.', 'middleware' => ['module:business_section']], function () {
            //product stock report
            Route::get('product-stock', 'ProductStockReportController@index')->name('product-stock');
            Route::post('ps-filter', 'ProductStockReportController@filter')->name('ps-filter');
            //product in wishlist report
            Route::get('product-in-wishlist', 'ProductWishlistReportController@index')->name('product-in-wishlist');
            Route::post('piw-filter', 'ProductWishlistReportController@filter')->name('piw-filter');
        });

        // Seller
        Route::group(['prefix' => 'sellers', 'as' => 'sellers.', 'middleware' => ['module:user_section']], function () {
            Route::get('add', 'SellerController@index')->name('add');
            Route::post('store', 'SellerController@store')->name('store');
            Route::get('seller-list', 'SellerController@list')->name('seller-list');
            Route::get('order-list/{seller_id}', 'SellerController@order_list')->name('order-list');
            Route::get('product-list/{seller_id}', 'SellerController@product_list')->name('product-list');
            Route::get('order-details/{order_id}/{seller_id}', 'SellerController@order_details')->name('order-details');
            Route::get('verification/{id}', 'SellerController@view')->name('verification');
            Route::get('view/{id}/{tab?}', 'SellerController@view')->name('view');
            Route::post('update-status', 'SellerController@updateStatus')->name('updateStatus');
            Route::post('withdraw-status/{id}', 'SellerController@withdrawStatus')->name('withdraw_status');
            Route::get('withdraw_list', 'SellerController@withdraw')->name('withdraw_list');
            Route::get('withdraw-view/{withdraw_id}/{seller_id}', 'SellerController@withdraw_view')->name('withdraw_view');
            Route::post('sales-commission-update/{id}', 'SellerController@sales_commission_update')->name('sales-commission-update');

            Route::get('reseller_list', 'SellerController@reseller_list')->name('reseller-list');
            Route::post('status-update', 'SellerController@status')->name('status-update');

            Route::post('admin-manage', 'SellerController@admin_manage_status')->name('admin_manage_status');
        });

        Route::group(['prefix' => 'product', 'as' => 'product.', 'middleware' => ['module:product_management']], function () {
            Route::get('add-new', 'ProductController@add_new')->name('add-new');
            Route::post('store', 'ProductController@store')->name('store');
            Route::get('remove-image', 'ProductController@remove_image')->name('remove-image');
            Route::post('status-update', 'ProductController@status_update')->name('status-update');
            Route::get('list/{type}', 'ProductController@list')->name('list');
            Route::get('stock-limit-list/{type}', 'ProductController@stock_limit_list')->name('stock-limit-list');
            Route::get('get-variations', 'ProductController@get_variations')->name('get-variations');
            Route::post('update-quarntity', 'ProductController@update_quantity')->name('update-quantity');
            //Route::get('list/{type}/{slug}', 'ProductController@list')->name('list');
            Route::get('edit/{id}', 'ProductController@edit')->name('edit');
            Route::post('update/{id}', 'ProductController@update')->name('update');
            Route::post('featured-status', 'ProductController@featured_status')->name('featured-status');
            Route::get('approve-status', 'ProductController@approve_status')->name('approve-status');
            Route::post('deny', 'ProductController@deny')->name('deny');
            Route::post('sku-combination', 'ProductController@sku_combination')->name('sku-combination');
            Route::get('get-categories', 'ProductController@get_categories')->name('get-categories');
            Route::delete('delete/{id}', 'ProductController@delete')->name('delete');

            Route::get('view/{id}', 'ProductController@view')->name('view');
            Route::get('bulk-import', 'ProductController@bulk_import_index')->name('bulk-import');
            Route::post('bulk-import', 'ProductController@bulk_import_data');
            Route::get('bulk-export', 'ProductController@bulk_export_data')->name('bulk-export');
            Route::get('product-manger', 'ProductController@product_manager')->name('product-manager');
            Route::post('product-manger', 'ProductController@product_manager_status')->name('product-manager-status');
        });


        // Service Management
        Route::group(['prefix' => 'service', 'as' => 'service.', 'middleware' => ['module:product_management']], function () {
            Route::get('list', 'ServiceController@list')->name('list');
            Route::get('add-new-service', 'ServiceController@add_new_service')->name('add-new-service');
            Route::get('apply-service-list', 'ServiceController@apply_service_list')->name('apply_service_list');
            Route::get('apply-service/{id}', 'ServiceController@apply_service_details')->name('apply_service_details');
            
            Route::get('show_update_page/{id}','ServiceController@show_update_page')->name('show_update_page');

            Route::post('store', 'ServiceController@store')->name('store');
            Route::post('update/{id}', 'ServiceController@update')->name('update');
            Route::delete('delete/{id}', 'ServiceController@delete')->name('delete');
            Route::delete('feature_delete/{id}', 'ServiceController@feature_delete')->name('feature_delete');
            Route::delete('image-delete/{id}', 'ServiceController@image_delete')->name('image_delete');
            Route::delete('apply-service-delete/{id}', 'ServiceController@apply_service_delete')->name('apply_service_delete');
            
            // Category
            Route::get('category/list', 'ServiceController@category_list')->name('category_list');
            Route::get('category/create', 'ServiceController@category_create_index')->name('category_create_index');
            Route::post('category/store', 'ServiceController@category_create_store')->name('category_create_store');
            Route::get('category/update/show/{id}','ServiceController@category_update_index')->name('category_update_index');
            Route::post('category/update/{id}', 'ServiceController@category_update_store')->name('category_update_store');
            Route::delete('category/delete/{id}', 'ServiceController@category_delete')->name('category_delete');


            // Banners
            Route::get('banners/list', 'ServiceController@banner_list')->name('banner_list');
            Route::get('banners/edit/{id}', 'ServiceController@banner_edit')->name('banner_edit');
            Route::post('banners/update/{id}', 'ServiceController@banner_update')->name('banner_update');
        });


        // Car Service Management
        Route::group(['prefix' => 'car', 'as' => 'car.', 'middleware' => ['module:product_management']], function () {
            Route::get('add-new-car', 'CarController@add_new_car')->name('add-new-car');
            Route::get('list', 'CarController@list')->name('list');
            Route::get('show_update_page/{id}', 'CarController@show_update_page')->name('show_update_page');
            Route::delete('delete/{id}', 'CarController@delete')->name('delete');

            Route::post('store', 'CarController@store')->name('store');
            Route::post('update/{id}', 'CarController@update')->name('update');
        });


        // Car Property Management
        Route::group(['prefix' => 'property', 'as' => 'property.', 'middleware' => ['module:product_management']], function () {
            Route::get('add-new-property', 'PropertyController@add_new_property')->name('add-new-property');
            Route::get('list', 'PropertyController@list')->name('list');
            Route::get('show_update_page/{id}', 'PropertyController@show_update_page')->name('show_update_page');
            Route::delete('delete/{id}', 'PropertyController@delete')->name('delete');

            Route::post('store', 'PropertyController@store')->name('store');
            Route::post('update/{id}', 'PropertyController@update')->name('update');
        });


        // Car Ambulance Management
        Route::group(['prefix' => 'ambulance', 'as' => 'ambulance.', 'middleware' => ['module:product_management']], function () {
            Route::get('show_update_page/', 'AmbulanceService@show_update_page')->name('show_update_page');

            Route::post('update/{id}', 'AmbulanceService@update')->name('update');
        });


        Route::group(['prefix' => 'transaction', 'as' => 'transaction.', 'middleware' => ['module:business_section']], function () {
            Route::get('list', 'TransactionController@list')->name('list');
        });

        Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.', 'middleware' => ['module:business_settings']], function () {
            Route::get('general-settings', 'BusinessSettingsController@index')->name('general-settings')->middleware('actch');;
            Route::get('update-language', 'BusinessSettingsController@update_language')->name('update-language');

            Route::get('about-us', 'BusinessSettingsController@about_us')->name('about-us');
            Route::post('about-us', 'BusinessSettingsController@about_usUpdate')->name('about-update');

            Route::get('future-plans', 'BusinessSettingsController@future_plans')->name('future_plans');
            Route::post('future-plans', 'BusinessSettingsController@future_plansUpdate')->name('future_plans_update');
            
            Route::get('contact-us', 'BusinessSettingsController@contact_us')->name('contact-us');
            Route::post('contact-us', 'BusinessSettingsController@contact_usUpdate')->name('contact-update');
            Route::get('return-and-refunds', 'BusinessSettingsController@return_and_refunds')->name('return-and-refunds');
            Route::post('return-and-refunds', 'BusinessSettingsController@return_and_refundsUpdate')->name('return-and-refunds-update');
            Route::post('update-info', 'BusinessSettingsController@updateInfo')->name('update-info');
            //Social Icon
            Route::get('social-media', 'BusinessSettingsController@social_media')->name('social-media');
            Route::get('fetch', 'BusinessSettingsController@fetch')->name('fetch');
            Route::post('social-media-store', 'BusinessSettingsController@social_media_store')->name('social-media-store');
            Route::post('social-media-edit', 'BusinessSettingsController@social_media_edit')->name('social-media-edit');
            Route::post('social-media-update', 'BusinessSettingsController@social_media_update')->name('social-media-update');
            Route::post('social-media-delete', 'BusinessSettingsController@social_media_delete')->name('social-media-delete');
            Route::post('social-media-status-update', 'BusinessSettingsController@social_media_status_update')->name('social-media-status-update');

            Route::get('terms-condition', 'BusinessSettingsController@terms_condition')->name('terms-condition');
            Route::post('terms-condition', 'BusinessSettingsController@updateTermsCondition')->name('update-terms');
            Route::get('privacy-policy', 'BusinessSettingsController@privacy_policy')->name('privacy-policy');
            Route::post('privacy-policy', 'BusinessSettingsController@privacy_policy_update')->name('privacy-policy');

            Route::get('fcm-index', 'BusinessSettingsController@fcm_index')->name('fcm-index');
            Route::post('update-fcm', 'BusinessSettingsController@update_fcm')->name('update-fcm');

            //captcha
            Route::get('captcha', 'BusinessSettingsController@recaptcha_index')->name('captcha');
            Route::post('recaptcha-update', 'BusinessSettingsController@recaptcha_update')->name('recaptcha_update');
            //google map api
            Route::get('map-api', 'BusinessSettingsController@map_api')->name('map-api');
            Route::post('map-api-update', 'BusinessSettingsController@map_api_update')->name('map-api-update');

            Route::post('update-fcm-messages', 'BusinessSettingsController@update_fcm_messages')->name('update-fcm-messages');

            Route::group(['prefix' => 'shipping-method', 'as' => 'shipping-method.', 'middleware' => ['module:business_settings']], function () {
                Route::get('by/admin', 'ShippingMethodController@index_admin')->name('by.admin');
                Route::get('by/seller', 'ShippingMethodController@index_seller')->name('by.seller');
                Route::post('add', 'ShippingMethodController@store')->name('add');
                Route::get('edit/{id}', 'ShippingMethodController@edit')->name('edit');
                Route::put('update/{id}', 'ShippingMethodController@update')->name('update');
                Route::post('delete', 'ShippingMethodController@delete')->name('delete');
                Route::post('status-update', 'ShippingMethodController@status_update')->name('status-update');
                Route::get('setting', 'ShippingMethodController@setting')->name('setting');
                Route::post('shipping-store', 'ShippingMethodController@shippingStore')->name('shipping-store');
            });

            Route::group(['prefix' => 'language', 'as' => 'language.', 'middleware' => ['module:business_settings']], function () {
                Route::get('', 'LanguageController@index')->name('index');
                //                Route::get('app', 'LanguageController@index_app')->name('index-app');
                Route::post('add-new', 'LanguageController@store')->name('add-new');
                Route::get('update-status', 'LanguageController@update_status')->name('update-status');
                Route::get('update-default-status', 'LanguageController@update_default_status')->name('update-default-status');
                Route::post('update', 'LanguageController@update')->name('update');
                Route::get('translate/{lang}', 'LanguageController@translate')->name('translate');
                Route::post('translate-submit/{lang}', 'LanguageController@translate_submit')->name('translate-submit');
                Route::post('remove-key/{lang}', 'LanguageController@translate_key_remove')->name('remove-key');
                Route::get('delete/{lang}', 'LanguageController@delete')->name('delete');
            });

            Route::group(['prefix' => 'mail', 'as' => 'mail.', 'middleware' => ['module:web_&_app_settings']], function () {
                Route::get('', 'MailController@index')->name('index')->middleware('actch');
                Route::post('update', 'MailController@update')->name('update');
            });

            Route::group(['prefix' => 'web-config', 'as' => 'web-config.', 'middleware' => ['module:web_&_app_settings']], function () {
                Route::get('/', 'BusinessSettingsController@companyInfo')->name('index')->middleware('actch');;
                Route::post('update-colors', 'BusinessSettingsController@update_colors')->name('update-colors');
                Route::post('update-language', 'BusinessSettingsController@update_language')->name('update-language');
                Route::post('update-company', 'BusinessSettingsController@updateCompany')->name('company-update');
                Route::post('update-company-email', 'BusinessSettingsController@updateCompanyEmail')->name('company-email-update');
                Route::post('update-company-phone', 'BusinessSettingsController@updateCompanyPhone')->name('company-phone-update');
                Route::post('upload-web-logo', 'BusinessSettingsController@uploadWebLogo')->name('company-web-logo-upload');
                Route::post('upload-mobile-logo', 'BusinessSettingsController@uploadMobileLogo')->name('company-mobile-logo-upload');
                Route::post('upload-footer-log', 'BusinessSettingsController@uploadFooterLog')->name('company-footer-logo-upload');
                Route::post('upload-fav-icon', 'BusinessSettingsController@uploadFavIcon')->name('company-fav-icon');
                Route::post('update-company-copyRight-text', 'BusinessSettingsController@updateCompanyCopyRight')->name('company-copy-right-update');
                Route::post('app-store/{name}', 'BusinessSettingsController@update')->name('app-store-update');
                Route::get('currency-symbol-position/{side}', 'BusinessSettingsController@currency_symbol_position')->name('currency-symbol-position');
                Route::post('order-banner', 'BusinessSettingsController@shop_banner')->name('order-banner');
            });
            Route::group(['prefix' => 'seller-settings', 'as' => 'seller-settings.', 'middleware' => ['module:business_settings']], function () {
                Route::get('/', 'BusinessSettingsController@seller_settings')->name('index')->middleware('actch');;
                Route::post('update-seller-settings', 'BusinessSettingsController@sales_commission')->name('update-seller-settings');
                Route::post('update-seller-registration', 'BusinessSettingsController@seller_registration')->name('update-seller-registration');
            });

            Route::group(['prefix' => 'payment-method', 'as' => 'payment-method.', 'middleware' => ['module:business_settings']], function () {
                Route::get('/', 'PaymentMethodController@index')->name('index')->middleware('actch');;
                Route::post('{name}', 'PaymentMethodController@update')->name('update');
            });

            Route::get('sms-module', 'SMSModuleController@sms_index')->name('sms-module');
            Route::post('sms-module-update/{sms_module}', 'SMSModuleController@sms_update')->name('sms-module-update');
        });

        //order management
        Route::group(['prefix' => 'orders', 'as' => 'orders.', 'middleware' => ['module:order_management']], function () {
            Route::get('list/{status}', 'OrderController@list')->name('list');
            Route::get('details/{id}', 'OrderController@details')->name('details');
            Route::post('status', 'OrderController@status')->name('status');
            Route::post('payment-status', 'OrderController@payment_status')->name('payment-status');
            Route::post('productStatus', 'OrderController@productStatus')->name('productStatus');
            Route::get('generate-invoice/{id}', 'OrderController@generate_invoice')->name('generate-invoice');
            Route::get('inhouse-order-filter', 'OrderController@inhouse_order_filter')->name('inhouse-order-filter');

            Route::get('add-delivery-man/{order_id}/{d_man_id}', 'OrderController@add_delivery_man')->name('add-delivery-man');

            Route::get('track-order/{id}', 'OrderController@track_order')->name('track-order');
        });
        
        //pos management
        Route::group(['prefix' => 'pos', 'as' => 'pos.'], function () {
            Route::get('/', 'POSController@index')->name('index');
            Route::get('quick-view', 'POSController@quick_view')->name('quick-view');
            Route::post('variant_price', 'POSController@variant_price')->name('variant_price');
            Route::post('add-to-cart', 'POSController@addToCart')->name('add-to-cart');
            Route::post('remove-from-cart', 'POSController@removeFromCart')->name('remove-from-cart');
            Route::post('cart-items', 'POSController@cart_items')->name('cart_items');
            Route::post('update-quantity', 'POSController@updateQuantity')->name('updateQuantity');
            Route::post('empty-cart', 'POSController@emptyCart')->name('emptyCart');
            Route::post('tax', 'POSController@update_tax')->name('tax');
            Route::post('discount', 'POSController@update_discount')->name('discount');
            Route::get('customers', 'POSController@get_customers')->name('customers');
            Route::post('order', 'POSController@place_order')->name('order');
            Route::get('orders', 'POSController@order_list')->name('orders');
            Route::get('order-details/{id}', 'POSController@order_details')->name('order-details');
            Route::get('invoice/{id}', 'POSController@generate_invoice');
        });

        Route::group(['prefix' => 'helpTopic', 'as' => 'helpTopic.', 'middleware' => ['module:web_&_app_settings']], function () {
            Route::get('list', 'HelpTopicController@list')->name('list');
            Route::post('add-new', 'HelpTopicController@store')->name('add-new');
            Route::get('status/{id}', 'HelpTopicController@status');
            Route::get('edit/{id}', 'HelpTopicController@edit');
            Route::post('update/{id}', 'HelpTopicController@update');
            Route::post('delete', 'HelpTopicController@destroy')->name('delete');
        });

        Route::group(['prefix' => 'contact', 'as' => 'contact.', 'middleware' => ['module:support_section']], function () {
            Route::post('contact-store', 'ContactController@store')->name('store');
            Route::get('list', 'ContactController@list')->name('list');
            Route::post('delete', 'ContactController@destroy')->name('delete');
            Route::get('view/{id}', 'ContactController@view')->name('view');
            Route::post('update/{id}', 'ContactController@update')->name('update');
            Route::post('send-mail/{id}', 'ContactController@send_mail')->name('send-mail');
        });

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

        Route::group(['prefix' => 'file-manager', 'as' => 'file-manager.'], function () {
            Route::get('/download/{file_name}', 'FileManagerController@download')->name('download');
            Route::get('/index/{folder_path?}', 'FileManagerController@index')->name('index');
            Route::post('/image-upload', 'FileManagerController@upload')->name('image-upload');
            Route::delete('/delete/{file_path}', 'FileManagerController@destroy')->name('destroy');
        });

        Route::group(['prefix' => 'blog_category', 'as' => 'blog_category.'], function () {
            Route::get('view', 'BlogCategoryController@index')->name('view');
            Route::get('fetch', 'BlogCategoryController@fetch')->name('fetch');
            Route::post('store', 'BlogCategoryController@store')->name('store');
            Route::get('edit/{id}', 'BlogCategoryController@edit')->name('edit');
            Route::post('update/{id}', 'BlogCategoryController@update')->name('update');
            Route::post('delete', 'BlogCategoryController@delete')->name('delete');
        });

        Route::group(['prefix' => 'blog_post', 'as' => 'blog_post.'], function () {
            Route::get('view', 'BlogPostController@index')->name('view');
            Route::get('fetch', 'BlogPostController@fetch')->name('fetch');
            Route::post('store', 'BlogPostController@store')->name('store');
            Route::get('edit/{id}', 'BlogPostController@edit')->name('edit');
            Route::post('update/{id}', 'BlogPostController@update')->name('update');
            Route::post('delete', 'BlogPostController@delete')->name('delete');
            Route::get('create/', 'BlogPostController@bloge_create')->name('bloge_create');

        });

        Route::group(['prefix' => 'post', 'as' => 'post.'], function () {
            Route::get('view', 'PostController@index')->name('view');
            Route::post('delete', 'PostController@delete')->name('delete');
            Route::post('status-update', 'PostController@status')->name('status-update');
        });


        Route::group(['prefix' => 'video_post', 'as' => 'video_post.'], function () {
            Route::get('view', 'VideoPostController@index')->name('view');
            Route::post('delete', 'VideoPostController@delete')->name('delete');
            Route::post('status-update', 'VideoPostController@status')->name('status-update');
        });

        Route::group(['prefix' => 'blog_comments', 'as' => 'blog_comments.'], function () {
            Route::get('view/{blog_post_id}', 'BlogCommentController@blog_comments_by_blog_post_id')->name('view');
            Route::post('reply-store', 'BlogCommentController@reply_store')->name('reply.store');
            Route::post('update_approvement', 'BlogCommentController@update_approvement')->name('update_approvement');
            Route::post('delete', 'BlogCommentController@delete_comment')->name('delete');
        });
    });

    //for test

    /*Route::get('login', 'testController@login')->name('login');*/
});
