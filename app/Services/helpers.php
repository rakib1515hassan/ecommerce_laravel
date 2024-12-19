<?php

use Illuminate\Support\Facades\App;
use App\Services\AdditionalServices;
use Illuminate\Support\Facades\Storage;

if (!function_exists('currency_symbol')) {
    function currency_symbol()
    {
        AdditionalServices::currency_load();
        if (\session()->has('currency_symbol')) {
            $symbol = \session('currency_symbol');
        } else {
            $system_default_currency_info = \session('system_default_currency_info');
            $symbol = $system_default_currency_info->symbol;
        }
        return $symbol;
    }
}

//formats currency
if (!function_exists('format_price')) {
    function format_price($price)
    {
        return number_format($price, 2) . currency_symbol();
    }
}

if (!function_exists('translate')) {
    function translate($key)
    {
        $local = AdditionalServices::default_lang();
        App::setLocale($local);

        $lang_array = include(base_path('resources/lang/' . $local . '/messages.php'));
        $processed_key = ucfirst(str_replace('_', ' ', AdditionalServices::remove_invalid_charcaters($key)));

        if (!array_key_exists($key, $lang_array)) {
            $lang_array[$key] = $processed_key;
            $str = "<?php return " . var_export($lang_array, true) . ";";
            file_put_contents(base_path('resources/lang/' . $local . '/messages.php'), $str);
            $result = $processed_key;
        } else {
            $result = __('messages.' . $key);
        }
        return $result;
    }
}

if (!function_exists('asset_storage')) {
    function asset_storage($path): string
    {
        return asset(Storage::url($path));
    }
}


if (!function_exists('permission_check')) {
    function permission_check($permission)
    {
        return AdditionalServices::module_permission_check($permission);
    }
}


if (!function_exists('admin_url')) {
    function admin_url()
    {
        return url('/');
    }
}
