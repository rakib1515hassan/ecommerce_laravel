<?php

use App\Http\Controllers\api\v1\GeneralController;
use App\Http\Controllers\api\v1\PaymentController;
use App\Http\Controllers\api\v1\PostController;
use App\Http\Controllers\api\v1\VideoPostController;
use App\Http\Controllers\api\v1\VideoCommentPostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\MembershipController;
use App\Http\Controllers\api\v1\auth\PassportAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::group(['namespace' => 'api\v1', 'prefix' => 'v1', 'middleware' => ['api_lang']], function () {

    Route::group(['prefix' => 'auth', 'namespace' => 'auth'], function () {
        Route::post('register', 'PassportAuthController@register');
        Route::post('login', 'PassportAuthController@login');

        Route::post('check-phone', 'PhoneVerificationController@check_phone');
        Route::post('verify-phone', 'PhoneVerificationController@verify_phone');

        Route::post('check-email', 'EmailVerificationController@check_email');
        Route::post('verify-email', 'EmailVerificationController@verify_email');

        Route::post('forgot-password', 'ForgotPassword@reset_password_request');
        Route::post('verify-otp', 'ForgotPassword@otp_verification_submit');
        Route::put('reset-password', 'ForgotPassword@reset_password_set');

        Route::any('social-login', 'SocialAuthController@social_login');
        Route::post('update-phone', 'SocialAuthController@update_phone');
    });

    Route::group(['prefix' => 'authorized', 'middleware' => 'auth:api'], function () {
        Route::post('change-password', [PassportAuthController::class, 'change_password']);
    });

    Route::group(['prefix' => 'config'], function () {
        Route::get('/', 'ConfigController@configuration');
    });

    Route::get('address', [GeneralController::class, 'address']);

    Route::group(['prefix' => 'shipping-method', 'middleware' => 'auth:api'], function () {
        Route::get('detail/{id}', 'ShippingMethodController@get_shipping_method_info');
        Route::get('by-seller/{id}/{seller_is}', 'ShippingMethodController@shipping_methods_by_seller');
        Route::post('choose-for-order', 'ShippingMethodController@choose_for_order');
        Route::get('chosen', 'ShippingMethodController@chosen_shipping_methods');
    });

    Route::group(['prefix' => 'cart', 'middleware' => 'auth:api'], function () {
        Route::get('/', 'CartController@cart');
        Route::get('shipping', 'CartController@cart_group_shipping_cost');
        Route::post('add', 'CartController@add_to_cart');
        Route::put('update', 'CartController@update_cart');
        Route::delete('remove', 'CartController@remove_from_cart');
    });

    Route::get('helper-page/faq', 'GeneralController@faq');
    Route::get('helper-page/{slug}', 'GeneralController@helper_page');

    Route::group(['prefix' => 'products'], function () {
        Route::get('latest', 'ProductController@get_latest_products');
        Route::get('featured', 'ProductController@get_featured_products');
        Route::get('top-rated', 'ProductController@get_top_rated_products');
        Route::any('search', 'ProductController@get_searched_products');
        Route::get('details/{id}', 'ProductController@get_product');
        Route::get('shipping-cost', 'ProductController@get_product_shipping_cost');
        Route::get('details-info/{slug}', 'ProductController@get_product_by_slug');

        // Route::get('details-info/{link}', 'ProductController@get_product_by_link');

        Route::get('related-products/{product_id}', 'ProductController@get_related_products');
        Route::get('reviews/{product_id}', 'ProductController@get_product_reviews');
        Route::get('rating/{product_id}', 'ProductController@get_product_rating');
        Route::get('rating_per_star/{product_id}', 'ProductController@get_product_rating_per_star');
        Route::get('counter/{product_id}', 'ProductController@counter');
        Route::get('shipping-methods', 'ProductController@get_shipping_methods');
        Route::get('social-share-link/{product_id}', 'ProductController@social_share_link');
        Route::post('reviews/submit', 'ProductController@submit_product_review')->middleware('auth:api');
        Route::get('best-selling-products', 'ProductController@get_best_selling_products');
        Route::get('best-sellings', 'ProductController@get_best_sellings');
        Route::get('home-categories', 'ProductController@get_home_categories');
        Route::get('discounted-product', 'ProductController@get_discounted_product');
        Route::get('/', 'ProductController@products');
        Route::get('/for-you', 'ProductController@product_for_you');

        Route::get('questions/{product_id}', 'QuestionAndAnsweringController@fetchQAByProductId');
        Route::post('questions/{product_id}/create', 'QuestionAndAnsweringController@createQAUnderTheProduct')->middleware('auth:api');
    });

    Route::group(['prefix' => 'notifications'], function () {
        Route::get('/', 'NotificationController@get_notifications');
    });

    Route::group(['prefix' => 'brands'], function () {
        Route::get('/', 'BrandController@get_brands');
        Route::get('/details/{brand_id}', 'BrandController@brand_details_by_brand_id');
        Route::get('/{brand_id}/products', 'BrandController@get_products');
    });

    Route::group(['prefix' => 'attributes'], function () {
        Route::get('/', 'AttributeController@get_attributes');
    });

    Route::group(['prefix' => 'flash-deals'], function () {
        Route::get('/', 'FlashDealController@get_flash_deal');
        Route::get('/all', 'FlashDealController@get_all_flash_deal');
        Route::get('/all/featured', 'FlashDealController@get_all_featured_flash_deal');
        Route::get('/{deal_id}/products', 'FlashDealController@get_products');
        Route::get('/{deal_id}/details', 'FlashDealController@flash_deal_details');
    });

    Route::group(['prefix' => 'deals'], function () {
        Route::get('featured', 'DealController@get_featured_deal');
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', 'CategoryController@get_categories');
        Route::get('/featured', 'CategoryController@get_featured_categories');
        Route::get('/details/{category_id}', 'CategoryController@category_details_by_id');
        Route::get('{category_id}/products', 'CategoryController@get_products');
    });

    Route::group(['prefix' => 'customer', 'middleware' => 'auth:api'], function () {
        Route::get('info', 'CustomerController@info');
        Route::put('update-profile', 'CustomerController@update_profile');
        Route::put('cm-firebase-token', 'CustomerController@update_cm_firebase_token');

        Route::post('memberships', [MembershipController::class, 'store']);
        Route::get('memberships/information/', [MembershipController::class, 'retrieve']);

        Route::post('memberships/points-conversion/', [MembershipController::class, 'points_conversion']);
        Route::get('coupons-list', [MembershipController::class, 'CustomerCoupons']);
        Route::get('memberships/referred-user', [MembershipController::class, 'all_referred']);
        Route::get('memberships/point/history-list', [MembershipController::class, 'point_history']);

        Route::group(['prefix' => 'address'], function () {
            Route::get('list', 'CustomerController@address_list');
            Route::post('add', 'CustomerController@add_new_address');
            Route::delete('/', 'CustomerController@delete_address');
            Route::post('edit', 'CustomerController@edit_address');
        });

        Route::group(['prefix' => 'support-ticket'], function () {
            Route::post('create', 'CustomerController@create_support_ticket');
            Route::get('get', 'CustomerController@get_support_tickets');
            Route::get('conv/{ticket_id}', 'CustomerController@get_support_ticket_conv');
            Route::post('reply/{ticket_id}', 'CustomerController@reply_support_ticket');
        });


        Route::group(['prefix' => 'wish-list'], function () {
            Route::get('/', 'CustomerController@wish_list');
            Route::post('add', 'CustomerController@add_to_wishlist');
            Route::delete('remove', 'CustomerController@remove_from_wishlist');
        });

        Route::group(['prefix' => 'order'], function () {
            Route::get('list', 'CustomerController@get_order_list');
            Route::get('list-with-details', 'CustomerController@get_order_list_with_details');
            Route::get('details', 'CustomerController@get_order_details');
            Route::get('place', 'OrderController@place_order');
            Route::post('cancel', 'CustomerController@cancel_order');
        });

        // Chatting
        Route::group(['prefix' => 'chat'], function () {
            Route::get('/', 'ChatController@chat_with_seller');
            Route::get('messages', 'ChatController@messages');
            Route::post('send-message', 'ChatController@messages_store');
        });
    });

    Route::group(['prefix' => 'order'], function () {
        Route::get('track', 'OrderController@track_order');
    });

    Route::group(['prefix' => 'payment'], function () {
        Route::get("all_methods", [PaymentController::class, 'index']);
    });

    Route::group(['prefix' => 'banners'], function () {
        Route::get('/', 'BannerController@get_banners');
    });

    Route::group(['prefix' => 'seller'], function () {
        Route::get('/', 'SellerController@get_seller_info');
        Route::get('{seller_id}/products', 'SellerController@get_seller_products');
        Route::get('top', 'SellerController@get_top_sellers');
        Route::get('all', 'SellerController@get_all_sellers');
    });

    Route::group(['prefix' => 'coupon', 'middleware' => 'auth:api'], function () {
        Route::get('apply', 'CouponController@apply');
    });

    //map api
    Route::group(['prefix' => 'mapapi'], function () {
        Route::get('place-api-autocomplete', 'MapApiController@place_api_autocomplete');
        Route::get('distance-api', 'MapApiController@distance_api');
        Route::get('place-api-details', 'MapApiController@place_api_details');
        Route::get('geocode-api', 'MapApiController@geocode_api');
    });

    Route::group(['prefix' => 'blog_categories'], function () {
        Route::get('/', 'BlogCategoryController@get_blog_categories');
        Route::get('/list', 'BlogCategoryController@getBlogCategories');
        Route::get('/details/{blog_category_id}', 'BlogCategoryController@get_blog_category_by_id');
        Route::get('/info/{blog_category_slug}', 'BlogCategoryController@get_blog_category_by_slug');
    });

    Route::group(['prefix' => 'blog_posts'], function () {
        Route::get('/', 'BlogPostController@get_blog_posts');
        Route::get('/details/{blog_post_id}', 'BlogPostController@get_blog_post_by_id');
        Route::get('/info/{blog_post_slug}', 'BlogPostController@get_blog_post_by_slug')->name('blog-post.find-by-slag');
        Route::get('/by_blog_category/{blog_category_id}', 'BlogPostController@get_blog_posts_by_blog_category_id');
        Route::get('/by_blog_category_slug/{blog_category_slug}', 'BlogPostController@get_blog_posts_by_blog_category_slug');
        Route::get('/random_blog_posts', 'BlogPostController@random_blog_posts');
        Route::get('/category_wise', 'BlogPostController@categoryWiseBlogPosts');
        Route::get('/auth-user', 'BlogPostController@authUserBlogPosts')->middleware('auth:api');

        Route::post('/save_blog_post', 'BlogPostController@save_blog_post')->middleware('auth:api');
        Route::put('/{id}/update', 'BlogPostController@updateBlogPost')->middleware('auth:api');
        Route::delete('/{id}/destroy', 'BlogPostController@destroyBlogPost')->middleware('auth:api');

        Route::get('/products', 'BlogPostController@get_products');

        Route::get('search/{title}', 'BlogPostController@searchBlogByTitle');
        Route::get('share-link/{blog_id}/make', 'BlogPostController@makeShareLink');
    });

    Route::group(['prefix' => 'blog_comments'], function () {
        Route::get('/', 'BlogCommentController@get_blog_comments');
        Route::get('/details/{blog_comment_id}', 'BlogCommentController@get_blog_comment_by_id');
        Route::get('/by_post/{blog_post_id}', 'BlogCommentController@get_blog_comments_by_blog_post_id');
        Route::get('/by_blog_post_slug/{blog_post_slug}', 'BlogCommentController@get_blog_comments_by_blog_post_slug');
        Route::post('/save_blog_comment', 'BlogCommentController@save_blog_comment')->middleware('auth:api');
    });

    Route::group(['prefix' => 'blog_visitors'], function () {
        Route::get('/', 'BlogVisitorController@get_blog_visitors');
        Route::get('/details/{blog_visitor_id}', 'BlogVisitorController@get_blog_visitor_by_id');
        Route::get('/by_blog_post/{blog_post_id}', 'BlogVisitorController@get_blog_visitors_by_blog_post_id');
        Route::get('/by_blog_post_slug/{blog_post_slug}', 'BlogVisitorController@get_blog_visitors_by_blog_post_slug');
    });

    Route::group(['prefix' => 'seo'], function () {
        Route::get('/product/{id}', 'SeoController@product');
    });



    Route::group(['prefix' => 'posts', 'as' => 'posts.'], function () {
        Route::get('', [PostController::class, 'index']);
        Route::get('my-posts', [PostController::class, 'userPosts']);
        Route::get('{slug}', [PostController::class, 'show']);
        Route::post('', [PostController::class, 'store']);
        Route::post('{slug}', [PostController::class, 'update']);
        Route::delete('{slug}', [PostController::class, 'destroy']);
        Route::get('{slug}/comments', [PostController::class, 'showComments']);
        Route::post('{slug}/comments', [PostController::class, 'storeComment']);
        Route::delete('{slug}/comments/{commentId}', [PostController::class, 'destroyComment']);
        Route::post('{slug}/reactions', [PostController::class, 'storeReaction']);
        Route::post('{slug}/report', [PostController::class, 'reportPost']);

    });

    Route::group(['prefix' => 'video_posts', 'as' => 'video_posts.'], function () {
        Route::get('', [VideoPostController::class, 'index']);
        Route::get('my-videos', [VideoPostController::class, 'userPosts']);
        Route::get('{slug}', [VideoPostController::class, 'show']);
        Route::post('', [VideoPostController::class, 'store']);
        Route::post('{slug}', [VideoPostController::class, 'update']);
        Route::delete('{slug}', [VideoPostController::class, 'destroy']);
        Route::get('{slug}/comments', [VideoPostController::class, 'showComments']);
        Route::post('{slug}/comments', [VideoPostController::class, 'storeComment']);
        Route::delete('{slug}/comments/{commentId}', [VideoPostController::class, 'destroyComment']);
        Route::post('{slug}/reactions', [VideoPostController::class, 'storeReaction']);
        Route::post('{slug}/report', [VideoPostController::class, 'reportPost']);
    });

    // Service
    Route::group(['prefix' => 'service'], function () {
        Route::get('/services/category/', 'ServiceController@category_list');
        Route::get('/services/banner/', 'ServiceController@banner_list');

        Route::get('/services', 'ServiceController@index');
        Route::get('/services/{id}', 'ServiceController@show');


        Route::get('/ambulance', 'AmbulanceController@show');
    });

    // Service
    Route::group(['prefix' => 'service', 'middleware' => 'auth:api'], function () {
        Route::post('apply', 'ServiceController@apply');
        Route::get('apply-services', 'ServiceController@applyServicesList');
        Route::get('apply-services/{id}', 'ServiceController@applyServiceDetail');
    });



    Route::fallback(function () {
        return response()->json([
            'success' => false,
            'error' => "Route not Found!"
        ], 404);
    });


});
