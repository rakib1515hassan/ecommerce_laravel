<?php

namespace App\Http\Controllers;

use App\Library\SslCommerz\SslCommerzPayment;
use App\Models\BusinessSetting;
use App\Models\Currency;
use App\Models\Order;
use App\Services\CartManager;
use App\Services\Converter;
use App\Services\AdditionalServices;
use App\Services\OrderManager;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SslCommerzPaymentController extends Controller
{

    public function index(Request $request)
    {
        $currency_model = AdditionalServices::get_business_settings('currency_model');
        if ($currency_model == 'multi_currency') {
            $currency_code = 'BDT';
        } else {
            $default = BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
            $currency_code = Currency::find($default)->code;
        }

        $discount = session()->has('coupon_discount') ? session('coupon_discount') : 0;
        $value = CartManager::cart_grand_total() - $discount;
        $user = AdditionalServices::get_customer($request);


        $post_data = array();
        $post_data['total_amount'] = Converter::usdTobdt($value);
        $post_data['currency'] = $currency_code;
        $post_data['tran_id'] = OrderManager::gen_unique_id(); // tran_id must be unique

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $user->f_name . ' ' . $user->l_name;
        $post_data['cus_email'] = $user->email;
        $post_data['cus_add1'] = $user->street_address == null ? 'address' : $user->user()->street_address;
        $post_data['cus_add2'] = "";
        $post_data['cus_city'] = "";
        $post_data['cus_state'] = "";
        $post_data['cus_postcode'] = "";
        $post_data['cus_country'] = "";
        $post_data['cus_phone'] = $user->phone;
        $post_data['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $post_data['ship_name'] = "Shipping";
        $post_data['ship_add1'] = "address 1";
        $post_data['ship_add2'] = "address 2";
        $post_data['ship_city'] = "City";
        $post_data['ship_state'] = "State";
        $post_data['ship_postcode'] = "ZIP";
        $post_data['ship_phone'] = "";
        $post_data['ship_country'] = "Country";

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = "Computer";
        $post_data['product_category'] = "Goods";
        $post_data['product_profile'] = "physical-goods";

        # OPTIONAL PARAMETERS
        $post_data['value_a'] = "ref001";
        $post_data['value_b'] = "ref002";
        $post_data['value_c'] = "ref003";
        $post_data['value_d'] = "ref004";

        try {
            $sslc = new SslCommerzPayment();
            $payment_options = $sslc->makePayment($post_data, 'hosted');
            if (!is_array($payment_options)) {
                echo "Payment gateway is not available!";
            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    public function success(Request $request)
    {
        $tran_id = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');


        $sslc = new SslCommerzPayment();
        $validation = $sslc->orderValidate($tran_id, $amount, $currency, $request->all());

//        $unique_id = OrderManager::gen_unique_id();
//        dd(CartManager::get_cart_group_ids($request));
//
//        $order_ids = [];
//        foreach (CartManager::get_cart_group_ids() as $group_id) {
//            dd($group_id);
//            $data = [
//                'payment_method' => 'sslcommerz',
//                'order_status' => 'confirmed',
//                'payment_status' => 'paid',
//                'transaction_ref' => $tran_id,
//                'order_group_id' => $unique_id,
//                'cart_group_id' => $group_id
//            ];
//            $order_id = OrderManager::generate_order($data);
//            $order_ids[] = $order_id;
//        }

        if ($validation) {
            DB::table('orders')->where('order_group_id', $tran_id)->update([
                'payment_status' => 'paid',
                'order_status' => 'confirmed',
                'payment_method' => 'ssl_commerz_payment',
                'transaction_ref' => $tran_id,
                'is_paid' => 1
            ]);
            return redirect(env('APP_URL') . '/user/orders?order_group_id=' . $tran_id);
        } else {
            DB::table('orders')->where('order_group_id', $tran_id)->update([
                'order_status' => 'failed'
            ]);
            return redirect(env('APP_URL') . '/cart?payment=failed');
        }
    }

    public function fail(Request $request)
    {
        return redirect(env('APP_URL') . '/cart?payment=failed');
    }

    public function cancel(Request $request)
    {
        return redirect(env('APP_URL') . '/cart?payment=cancel');
    }
}
