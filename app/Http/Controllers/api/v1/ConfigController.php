<?php

namespace App\Http\Controllers\api\v1;

use App\Services\AdditionalServices;
use App\Services\ProductManager;
use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Currency;
use App\Models\HelpTopic;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

if (!function_exists('asset_storage')) {
    function asset_storage($path): string
    {
        return asset(Storage::url($path));
    }
}

class ConfigController extends Controller
{
    public function configuration(): JsonResponse
    {
        $currency = Currency::all();
        $social_login = [];
        foreach (AdditionalServices::get_business_settings('social_login') as $social) {
            $config = [
                'login_medium' => $social['login_medium'],
                'status' => (boolean)$social['status']
            ];
            $social_login[] = $config;
        }

        $languages = AdditionalServices::get_business_settings('pnc_language');
        $lang_array = [];
        foreach ($languages as $language) {
            $lang_array[] = [
                'code' => $language,
                'name' => AdditionalServices::get_language_name($language)
            ];
        }

        return response()->json([
            'system_default_currency' => (int)AdditionalServices::get_business_settings('system_default_currency'),
            'digital_payment' => (boolean)AdditionalServices::get_business_settings('digital_payment')['status'] ?? 0,
            'cash_on_delivery' => (boolean)AdditionalServices::get_business_settings('cash_on_delivery')['status'] ?? 0,
            'base_urls' => [
                'product_image_url' => asset_storage('product'),
                'product_thumbnail_url' => asset_storage('product'),
                'brand_image_url' => asset_storage('brand'),
                'customer_image_url' => asset_storage('profile'),
                'banner_image_url' => asset_storage('banner'),
                'category_image_url' => asset_storage('category'),
                'review_image_url' => asset_storage('app/public'),
                'seller_image_url' => asset_storage('seller'),
                'shop_image_url' => asset_storage('order'),
                'notification_image_url' => asset_storage('notification'),
                'blog_post' => asset_storage('blog_post'),
                'post_image' => asset_storage('post_image'),
                'post_video' => asset_storage('post_video'),
            ],
            'static_urls' => [
                'contact_us' => "",
                'brands' => "",
                'categories' => "",
                'customer_account' => ""
            ],
            'about_us' => AdditionalServices::get_business_settings('about_us'),
            'privacy_policy' => AdditionalServices::get_business_settings('privacy_policy'),
            'faq' => HelpTopic::all(),
            'terms_&_conditions' => AdditionalServices::get_business_settings('terms_condition'),
            'currency_list' => $currency,
            'currency_symbol_position' => AdditionalServices::get_business_settings('currency_symbol_position') ?? 'right',
            'maintenance_mode' => (boolean)AdditionalServices::get_business_settings('maintenance_mode') ?? 0,
            'language' => $lang_array,
            'colors' => Color::all(),
            'unit' => AdditionalServices::units(),
            'shipping_method' => AdditionalServices::get_business_settings('shipping_method'),
            'email_verification' => (boolean)AdditionalServices::get_business_settings('email_verification'),
            'phone_verification' => (boolean)AdditionalServices::get_business_settings('phone_verification'),
            'country_code' => AdditionalServices::get_business_settings('country_code'),
            'social_login' => $social_login,
            'currency_model' => AdditionalServices::get_business_settings('currency_model'),
            'forgot_password_verification' => AdditionalServices::get_business_settings('forgot_password_verification'),
            'announcement' => AdditionalServices::get_business_settings('announcement'),
            'app_version' => '1.0.0',
        ]);
    }
}

