<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Color;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Review;
use App\Models\Seller;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

function get_discounted_price($price, $discount, $discount_type)
{
    if ($discount_type == 'flat') {
        return $price - $discount;
    } elseif ($discount_type == 'percent') {
        return $price - ($price * ($discount / 100));
    } else
        return $price;
}


function get_discount($price, $discount, $discount_type)
{
    if ($discount_type == 'flat') {
        return $discount;
    } elseif ($discount_type == 'percent') {
        return $price * ($discount / 100);
    } else
        return 0;
}

class AdditionalServices
{
    public static function status($id)
    {
        if ($id == 1) {
            $x = 'active';
        } elseif ($id == 0) {
            $x = 'in-active';
        }

        return $x;
    }

    public static function transaction_formatter($transaction)
    {
        if ($transaction['paid_by'] == 'customer') {
            $user = User::find($transaction['payer_id']);
            $payer = $user->f_name . ' ' . $user->l_name;
        } elseif ($transaction['paid_by'] == 'seller') {
            $user = Seller::find($transaction['payer_id']);
            $payer = $user->f_name . ' ' . $user->l_name;
        } elseif ($transaction['paid_by'] == 'admin') {
            $user = Admin::find($transaction['payer_id']);
            $payer = $user->name;
        }

        if ($transaction['paid_to'] == 'customer') {
            $user = User::find($transaction['payment_receiver_id']);
            $receiver = $user->f_name . ' ' . $user->l_name;
        } elseif ($transaction['paid_to'] == 'seller') {
            $user = Seller::find($transaction['payment_receiver_id']);
            $receiver = $user->f_name . ' ' . $user->l_name;
        } elseif ($transaction['paid_to'] == 'admin') {
            $user = Admin::find($transaction['payment_receiver_id']);
            $receiver = $user->name;
        }

        $transaction['payer_info'] = $payer;
        $transaction['receiver_info'] = $receiver;

        return $transaction;
    }

    public static function get_customer($request = null)
    {
        $user = null;
        if (auth('customer')->check()) {
            $user = auth('customer')->user(); // for web
        } elseif ($request != null && $request->user() != null) {
            $user = $request->user(); //for api
        } elseif (session()->has('customer_id')) {
            $user = User::find(session('customer_id'));
        }

        if ($user == null) {
            $user = 'offline';
        }

        return $user;
    }

    // public static function coupon_discount($request)
    // {
    //     Log::info('Coupon discount calculation started', ['request' => $request]);
    //     $discount = 0;
    //     $user = AdditionalServices::get_customer($request);
    //     $couponLimit = Order::where('customer_id', $user->id)
    //         ->where('coupon_code', $request['coupon_code'])->count();


    //     $coupon = Coupon::where(['code' => $request['coupon_code']])
    //         ->where('limit', '>', $couponLimit)
    //         ->where('status', '=', 1)
    //         ->whereDate('start_date', '<=', Carbon::parse()->toDateString())
    //         ->whereDate('expire_date', '>=', Carbon::parse()->toDateString())->first();
            
    //         Log::info('Coupon retrieved', ['coupon' => $coupon]);

    //     if (isset($coupon)) {
    //         $total = 0;
    //         foreach (CartManager::get_cart(CartManager::get_cart_group_ids($request)) as $cart) {
    //             $product_subtotal = $cart['price'] * $cart['quantity'];
    //             $total += $product_subtotal;

    //             Log::info('Total cart amount calculated', ['total' => $total]);
    //         }
    //         if ($total >= $coupon['min_purchase']) {
    //             if ($coupon['discount_type'] == 'percentage') {
    //                 $discount = min((($total / 100) * $coupon['discount']), $coupon['max_discount']);
    //                 Log::info('Percentage discount calculated', ['discount' => $discount]);
    //             } else {
    //                 $discount = $coupon['discount'];
    //                 Log::info('Flat discount applied', ['discount' => $discount]);
    //             }
    //         }
    //         elseif($total >= $coupon['min_purchase']) {
    //             if ($coupon['discount_type'] == 'amount') {
    //                 $discount = min((($total) - $coupon['discount']), $coupon['max_discount']);
    //                 Log::info('Amount discount calculated', ['discount' => $discount]);
    //             } else {
    //                 $discount = $coupon['discount'];
    //                 Log::info('Flat discount applied', ['discount' => $discount]);
    //             }
    //         }
    //     } else {
    //         Log::warning('Coupon not valid or not found', ['coupon_code' => $request['coupon_code']]);
    //     }
    
    //     Log::info('Coupon discount calculation completed', ['discount' => $discount]);

    //     return $discount;
    // }

    public static function coupon_discount($request)
    {
        $discount = 0;
        $user = AdditionalServices::get_customer($request);
        $couponLimit = Order::where('customer_id', $user->id)
            ->where('coupon_code', $request['coupon_code'])->count();
    
        $coupon = Coupon::where(['code' => $request['coupon_code']])
            ->where('limit', '>', $couponLimit)
            ->where('status', '=', 1)
            ->whereDate('start_date', '<=', Carbon::parse()->toDateString())
            ->whereDate('expire_date', '>=', Carbon::parse()->toDateString())->first();
    
        if (isset($coupon)) {
            $total = 0;
            foreach (CartManager::get_cart(CartManager::get_cart_group_ids($request)) as $cart) {
                $product_subtotal = $cart['price'] * $cart['quantity'];
                $total += $product_subtotal;
                Log::info('Total before discount: ', ['total' => $total]);
            }
            if ($total >= $coupon['min_purchase']) {
                if ($coupon['discount_type'] == 'percentage') {
                    $discount = min((($total / 100) * $coupon['discount']), $coupon['max_discount']);
                    Log::info('Discount applied: ', ['discount' => $discount]);
                } else {
                    $discount = $coupon['discount'];
                    Log::info('Discount applied: ', ['discount' => $discount]);
                }
            }
        }
    
        return $discount;
    }
    


    public static function default_lang()
    {
        if (strpos(url()->current(), '/api')) {
            $lang = App::getLocale();
        } elseif (session()->has('local')) {
            $lang = session('local');
        } else {
            $data = AdditionalServices::get_business_settings('language');
            $code = 'en';
            $direction = 'ltr';
            foreach ($data as $ln) {
                if (array_key_exists('default', $ln) && $ln['default']) {
                    $code = $ln['code'];
                    if (array_key_exists('direction', $ln)) {
                        $direction = $ln['direction'];
                    }
                }
            }
            session()->put('local', $code);
            Session::put('direction', $direction);
            $lang = $code;
        }
        return $lang;
    }

    public static function rating_count($product_id, $rating)
    {
        return Review::where(['product_id' => $product_id, 'rating' => $rating])->count();
    }

    public static function get_business_settings($name)
    {
        $config = null;
        $check = ['currency_model', 'currency_symbol_position', 'system_default_currency', 'language', 'company_name'];

        if (in_array($name, $check) && session()->has($name)) {
            $config = session($name);
        } else {
            $data = BusinessSetting::where(['type' => $name])->first();
            if (isset($data)) {
                $config = json_decode($data['value'], true);
                if (is_null($config)) {
                    $config = $data['value'];
                }
            }

            if (in_array($name, $check)) {
                session()->put($name, $config);
            }
        }

        return $config;
    }

    public static function get_settings($object, $type)
    {
        $config = null;
        foreach ($object as $setting) {
            if ($setting['type'] == $type) {
                $config = $setting;
            }
        }
        return $config;
    }

    public static function get_shipping_methods($seller_id, $type)
    {
        return ShippingMethod::where(['status' => 1])->where(['creator_id' => $seller_id, 'creator_type' => $type])->get();
    }

    public static function get_image_path($type): string
    {
        $path = asset('storage/brand');
        return $path;
    }

    public static function product_data_formatting_paginate($data)
    {
        return self::getThrough($data);
    }

    public static function flash_sale_product_data_formatting_paginate($data)
    {
        return self::getThrough($data);
    }

    public static function product_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data) {
            foreach ($data as $item) {
                $item = self::getItem($item);
                $storage[] = $item;
            }
            $data = $storage;
        } else {
            $data = self::getItem($data);
        }

        return $data;
    }

    public static function product_info_formatting($data, $multi_data = false)
    {
        $wishlist = Wishlist::where('product_id', $data->id)->first();

        $storage = [];
        if ($multi_data) {
            foreach ($data as $item) {
                $variation = [];
                $item['category_ids'] = json_decode($item['category_ids']);
                $item['images'] = json_decode($item['images']);
                $item['colors'] = Color::whereIn('code', json_decode($item['colors']))->get(['name', 'code']);
                $attributes = [];
                if (json_decode($item['attributes']) != null) {
                    foreach (json_decode($item['attributes']) as $attribute) {
                        array_push($attributes, (integer)$attribute);
                    }
                }
                $item = self::getItem1($attributes, $item, $variation);
                $storage[] = $item;
            }
            $data = $storage;
        } else {
            $data = self::getItem($data);
        }

        $data['in_wishlist'] = (bool)$wishlist;

        return $data;
    }

    public static function units()
    {
        $x = ['kg', 'pc', 'gms', 'ltrs'];
        return $x;
    }

    public static function remove_invalid_charcaters($str): array|string
    {
        return str_ireplace(['\'', '"', ',', ';', '<', '>', '?'], ' ', $str);
    }

    public static function saveJSONFile($code, $data): void
    {
        ksort($data);
        $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents(base_path('resources/lang/en/messages.json'), stripslashes($jsonData));
    }

    public static function combinations($arrays): array
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    public static function error_processor($validator): array
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            $err_keeper[] = ['code' => $index, 'message' => $error[0]];
        }
        return $err_keeper;
    }

    public static function currency_load(): void
    {
        $default = AdditionalServices::get_business_settings('system_default_currency');
        $current = \session('system_default_currency_info');
        if (!session()->has('system_default_currency_info') || $default != $current['id']) {
            $id = AdditionalServices::get_business_settings('system_default_currency');
            $currency = Currency::find($id);
            session()->put('system_default_currency_info', $currency);
            session()->put('currency_code', $currency->code);
            session()->put('currency_symbol', $currency->symbol);
            session()->put('currency_exchange_rate', $currency->exchange_rate);
        }
    }

    public static function currency_converter($amount): string
    {
        $currency_model = AdditionalServices::get_business_settings('currency_model');
        if ($currency_model == 'multi_currency') {
            if (session()->has('usd')) {
                $usd = session('usd');
            } else {
                $usd = Currency::where(['code' => 'USD'])->first()->exchange_rate;
                session()->put('usd', $usd);
            }
            $my_currency = \session('currency_exchange_rate');
            $rate = $my_currency / $usd;
        } else {
            $rate = 1;
        }

        return AdditionalServices::set_symbol(round($amount * $rate, 2));
    }

    public static function language_load()
    {
        if (\session()->has('language_settings')) {
            $language = \session('language_settings');
        } else {
            $language = BusinessSetting::where('type', 'language')->first();
            \session()->put('language_settings', $language);
        }
        return $language;
    }

    public static function tax_calculation($price, $tax, $tax_type)
    {
        $amount = ($price / 100) * $tax;
        return $amount;
    }

    public static function get_price_range($product): string
    {
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;

        foreach (json_decode($product->variation) as $key => $variation) {
            if ($lowest_price > $variation->price) {
                $lowest_price = round($variation->price, 2);
            }
            if ($highest_price < $variation->price) {
                $highest_price = round($variation->price, 2);
            }
        }

        $lowest_price = AdditionalServices::currency_converter($lowest_price - AdditionalServices::get_product_discount($product, $lowest_price));
        $highest_price = AdditionalServices::currency_converter($highest_price - AdditionalServices::get_product_discount($product, $highest_price));

        if ($lowest_price == $highest_price) {
            return $lowest_price;
        }
        return $lowest_price . ' - ' . $highest_price;
    }

    public static function get_product_discount($product, $price): float
    {
        $discount = 0;
        if ($product->discount_type == 'percent') {
            $discount = ($price * $product->discount) / 100;
        } elseif ($product->discount_type == 'flat') {
            $discount = $product->discount;
        }

        return floatval($discount);
    }

    public static function get_discount($type, $discount, $price): float
    {
        $d = 0;
        if ($type == 'percent') {
            $d = ($price * $discount) / 100;
        } elseif ($type == 'flat') {
            $d = $discount;
        }

        return floatval($d);
    }

    public static function module_permission_check($mod_name): bool
    {
        $permission = auth('admin')->user()->role->module_access;
        if (isset($permission) && in_array($mod_name, (array)json_decode($permission))) {
            return true;
        }

        if (auth('admin')->user()->admin_role_id == 1) {
            return true;
        }
        return false;
    }

    public static function convert_currency_to_usd($price): float|int
    {
        $currency_model = AdditionalServices::get_business_settings('currency_model');
        if ($currency_model == 'multi_currency') {
            AdditionalServices::currency_load();
            $code = session('currency_code') == null ? 'USD' : session('currency_code');
            $currency = Currency::where('code', $code)->first();
            $price = floatval($price) / floatval($currency->exchange_rate);
        } else {
            $price = floatval($price);
        }

        return $price;
    }

    public static function order_status_update_message($status)
    {
        if ($status == 'pending') {
            $data = BusinessSetting::where('type', 'order_pending_message')->first()->value;
        } elseif ($status == 'confirmed') {
            $data = BusinessSetting::where('type', 'order_confirmation_msg')->first()->value;
        } elseif ($status == 'processing') {
            $data = BusinessSetting::where('type', 'order_processing_message')->first()->value;
        } elseif ($status == 'out_for_delivery') {
            $data = BusinessSetting::where('type', 'out_for_delivery_message')->first()->value;
        } elseif ($status == 'delivered') {
            $data = BusinessSetting::where('type', 'order_delivered_message')->first()->value;
        } elseif ($status == 'returned') {
            $data = BusinessSetting::where('type', 'order_returned_message')->first()->value;
        } elseif ($status == 'failed') {
            $data = BusinessSetting::where('type', 'order_failed_message')->first()->value;
        } elseif ($status == 'delivery_boy_delivered') {
            $data = BusinessSetting::where('type', 'delivery_boy_delivered_message')->first()->value;
        } elseif ($status == 'del_assign') {
            $data = BusinessSetting::where('type', 'delivery_boy_assign_message')->first()->value;
        } elseif ($status == 'ord_start') {
            $data = BusinessSetting::where('type', 'delivery_boy_start_message')->first()->value;
        } else {
            $data = '{"status":"0","message":""}';
        }

        $res = json_decode($data, true);

        if ($res['status'] == 0) {
            return 0;
        }
        return $res['message'];
    }

    public static function send_push_notif_to_device($fcm_token, $data)
    {
        $key = BusinessSetting::where(['type' => 'push_notification_key'])->first()->value;
        $url = "https://fcm.googleapis.com/fcm/send";
        $header = array("authorization: key=" . $key . "",
            "content-type: application/json"
        );

        if (!isset($data['order_id'])) {
            $data['order_id'] = null;
        }

        $postdata = '{
            "to" : "' . $fcm_token . '",
            "data" : {
                "title" :"' . $data['title'] . '",
                "body" : "' . $data['description'] . '",
                "image" : "' . $data['image'] . '",
                "order_id":"' . $data['order_id'] . '",
                "is_read": 0
              },
              "notification" : {
                "title" :"' . $data['title'] . '",
                "body" : "' . $data['description'] . '",
                "image" : "' . $data['image'] . '",
                "order_id":"' . $data['order_id'] . '",
                "title_loc_key":"' . $data['order_id'] . '",
                "is_read": 0,
                "icon" : "new",
                "sound" : "default"
              }
        }';

        return self::extracted($url, $postdata, $header);
    }

    public static function send_push_notif_to_topic($data)
    {
        $key = BusinessSetting::where(['type' => 'push_notification_key'])->first()->value;

        $url = "https://fcm.googleapis.com/fcm/send";
        $header = ["authorization: key=" . $key . "",
            "content-type: application/json",
        ];

        $image = asset('storage/notification') . '/' . $data['image'];
        $postdata = '{
            "to" : "/topics/shukhimart",
            "data" : {
                "title":"' . $data->title . '",
                "body" : "' . $data->description . '",
                "image" : "' . $image . '",
                "is_read": 0
              },
              "notification" : {
                "title":"' . $data->title . '",
                "body" : "' . $data->description . '",
                "image" : "' . $image . '",
                "title_loc_key":"' . $data['order_id'] . '",
                "is_read": 0,
                "icon" : "new",
                "sound" : "default"
              }
        }';

        return self::extracted($url, $postdata, $header);
    }

    public static function get_seller_by_token($request)
    {
        $data = '';
        $success = 0;

        $token = explode(' ', $request->header('authorization'));
        if (count($token) > 1 && strlen($token[1]) > 30) {
            $seller = Seller::where(['auth_token' => $token['1']])->first();
            if (isset($seller)) {
                $data = $seller;
                $success = 1;
            }
        }

        return [
            'success' => $success,
            'data' => $data
        ];
    }

    public static function remove_dir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") AdditionalServices::remove_dir($dir . "/" . $object); else unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function currency_code()
    {
        AdditionalServices::currency_load();
        if (session()->has('currency_symbol')) {
            $symbol = session('currency_symbol');
            $code = Currency::where(['symbol' => $symbol])->first()->code;
        } else {
            $system_default_currency_info = session('system_default_currency_info');
            $code = $system_default_currency_info->code;
        }
        return $code;
    }

    public static function get_language_name($key)
    {
        $values = AdditionalServices::get_business_settings('language');
        foreach ($values as $value) {
            if ($value['code'] == $key) {
                $key = $value['name'];
            }
        }

        return $key;
    }

    public static function setEnvironmentValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        $oldValue = env($envKey);
        if (str_contains($str, $envKey)) {
            $str = str_replace("{$envKey}={$oldValue}", "{$envKey}={$envValue}", $str);
        } else {
            $str .= "{$envKey}={$envValue}\n";
        }
        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
        return $envValue;
    }


    public static function sales_commission($order)
    {
        $order_summery = OrderManager::order_summary($order);
        $order_total = $order_summery['subtotal'] - $order_summery['total_discount_on_product'] - $order['discount_amount'];
        $commission_amount = 0;

        if ($order['seller_is'] == 'seller') {
            $seller = Seller::find($order['seller_id']);
            if (isset($seller) && $seller['sales_commission_percentage'] !== null) {
                $commission = $seller['sales_commission_percentage'];
            } else {
                $commission = AdditionalServices::get_business_settings('sales_commission');
            }
            $commission_amount = (($order_total / 100) * $commission);
        }
        return $commission_amount;
    }

    public static function categoryName($id)
    {
        return Category::select('name')->find($id)->name;
    }

    public static function set_symbol($amount)
    {
        $position = AdditionalServices::get_business_settings('currency_symbol_position');
        if ($position == 'left') {
            $string = currency_symbol() . '' . number_format($amount, 2);
        } else {
            $string = number_format($amount, 2) . '' . currency_symbol();
        }
        return $string;
    }

    public static function pagination_limit()
    {
        $pagination_limit = BusinessSetting::where('type', 'pagination_limit')->first();
        if ($pagination_limit != null) {
            return $pagination_limit->value;
        } else {
            return 25;
        }

    }

    public static function gen_mpdf($view, $file_prefix, $file_postfix)
    {
        $mpdf = new \Mpdf\Mpdf(['default_font' => 'FreeSerif', 'mode' => 'utf-8', 'format' => [190, 250], 'tempDir'=>storage_path('temp')]);
        /* $mpdf->AddPage('XL', '', '', '', '', 10, 10, 10, '10', '270', '');*/
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $mpdf_view = $view;
        $mpdf_view = $mpdf_view->render();
        $mpdf->WriteHTML($mpdf_view);
        $mpdf->Output($file_prefix . $file_postfix . '.pdf', 'D');
    }

    
    /**
     * @param $data
     * @return mixed
     */
    public static function getThrough($data): mixed
    {
        return $data->through(function ($item) {
            $item = self::getItem($item);

            return $item;
        });
    }

    /**
     * @param mixed $item
     * @return mixed
     */
    public static function getItem(mixed $item): mixed
    {
        $variation = [];
        $item['category_ids'] = json_decode($item['category_ids']);
        $item['images'] = json_decode($item['images']);
        $item['colors'] = Color::whereIn('code', json_decode($item['colors']))->get(['name', 'code']);
        $attributes = [];
        if (json_decode($item['attributes']) != null) {
            foreach (json_decode($item['attributes']) as $attribute) {
                $attributes[] = (integer)$attribute;
            }
        }
        $item = self::getItem1($attributes, $item, $variation);
        return $item;
    }

    /**
     * @param array $attributes
     * @param mixed $item
     * @param array $variation
     * @return mixed
     */
    public static function getItem1(array $attributes, mixed $item, array $variation): mixed
    {
        $item['attributes'] = $attributes;
        $item['choice_options'] = json_decode($item['choice_options']);
        foreach (json_decode($item['variation'], true) as $var) {
            $variation[] = [
                'type' => $var['type'],
                'price' => (double)$var['price'],
                'sku' => $var['sku'],
                'qty' => (integer)$var['qty'],
            ];
        }
        $item['variation'] = $variation;
        return $item;
    }

    /**
     * @param string $url
     * @param string $postdata
     * @param array $header
     * @return bool|string
     */
    public static function extracted(string $url, string $postdata, array $header): string|bool
    {
        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);
        // close handle to release resources
        curl_close($ch);

        return $result;
    }
}
