<?php

namespace App\Services;

use App\Library\SslCommerz\SslCommerzPayment;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    static function all_payment_methods(): array
    {
        return [
            [
                'name' => 'Cash On Delivery',
                'slug' => 'cash_on_delivery',
                'description' => 'Pay with cash upon delivery.',
                'image' => asset('/assets/payment/cod.jpeg'),
                'status' => 1,
            ], [
                'name' => 'SSLCommerz',
                'slug' => 'ssl_commerz_payment',
                'description' => 'Pay with SSLCommerz.',
                'image' => asset('/assets/payment/sslcommerz.png'),
                'status' => 1,
            ]
        ];

    }


    // function cod_payment($request)
    // {
    //     $order_group_id = OrderManager::gen_unique_id();
    //     $order_ids = [];

    //     foreach (CartManager::get_cart_group_ids($request) as $group_id) {
    //         $data = [
    //             'payment_method' => 'cash_on_delivery',
    //             'order_status' => 'pending',
    //             'payment_status' => 'unpaid',
    //             'transaction_ref' => '',
    //             'order_group_id' => $order_group_id,
    //             'cart_group_id' => $group_id,
    //             'coupon_code' => $request->coupon_code ?? null,
    //             'request' => $request,
    //         ];

            
    //         Log::info('cod payment', ['order_id' => $data]);
    //         $order_id = OrderManager::generate_order($data);


    //         $order = Order::find($order_id);

    //         $order->billing_address = ($request['billing_address_id'] != null) ? $request['billing_address_id'] : $order['billing_address'];
    //         $order->billing_address_data = ($request['billing_address_id'] != null) ? ShippingAddress::find($request['billing_address_id']) : $order['billing_address_data'];
    //         $order->order_note = ($request['order_note'] != null) ? $request['order_note'] : $order['order_note'];
    //         $order->save();

    //         $order_ids[] = $order_id;
    //     }

    //     CartManager::cart_clean($request);

    //     return [
    //         'status' => true,
    //         'has_redirection' => false,
    //         'redirect_url' => null,
    //         'message' => translate('order_placed_successfully'),
    //         'order_ids' => $order_ids,
    //     ];
    // }

    public function cod_payment($request)
    {
        $order_group_id = OrderManager::gen_unique_id();
        $order_ids = [];

        foreach (CartManager::get_cart_group_ids($request) as $group_id) {
            $data = [
                'payment_method' => 'cash_on_delivery',
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
                'transaction_ref' => '',
                'order_group_id' => $order_group_id,
                'cart_group_id' => $group_id,
                'coupon_code' => $request->coupon_code ?? null,
                'request' => $request,
            ];

            $order_id = OrderManager::generate_order($data);

            $order = Order::find($order_id);

            $order->billing_address = ($request['billing_address_id'] != null) ? $request['billing_address_id'] : $order['billing_address'];
            $order->billing_address_data = ($request['billing_address_id'] != null) ? ShippingAddress::find($request['billing_address_id']) : $order['billing_address_data'];
            $order->order_note = ($request['order_note'] != null) ? $request['order_note'] : $order['order_note'];
            $order->save();

            $order_ids[] = $order_id;
        }

        CartManager::cart_clean($request);

        return [
            'status' => true,
            'has_redirection' => false,
            'redirect_url' => null,
            'message' => translate('order_placed_successfully'),
            'order_ids' => $order_ids,
        ];
    }



    public function ssl_commerz_payment($request)
    {
        $currency_code = 'BDT';
        $amount = 0;
        $user = AdditionalServices::get_customer($request);
        $shipping_address = ShippingAddress::find($request->address_id);
        $cart = Cart::where('customer_id', $user->id)->get();

        $order_group_id = OrderManager::gen_unique_id();
        foreach (CartManager::get_cart_group_ids($request) as $group_id) {
            $data = [
                'payment_method' => 'ssl_commerz_payment',
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
                'transaction_ref' => '',
                'order_group_id' => $order_group_id,
                'cart_group_id' => $group_id,
                'coupon_code' => $request->coupon_code ?? null,
                'request' => $request,
            ];
            $order_id = OrderManager::generate_order($data);

            $order = Order::find($order_id);
            $order->billing_address = ($request['billing_address_id'] != null) ? $request['billing_address_id'] : $order['billing_address'];
            $order->billing_address_data = ($request['billing_address_id'] != null) ? ShippingAddress::find($request['billing_address_id']) : $order['billing_address_data'];
            $order->order_note = ($request['order_note'] != null) ? $request['order_note'] : $order['order_note'];
            $order->save();

            $amount += $order->order_amount;
        }

        CartManager::cart_clean($request);


        $product_name = "";
        $product_category = "";
        foreach ($cart as $key => $value) {
            $product = Product::with('brand')->find($value['product_id']);
            $product_name .= $product->name . ', ';
            $product_category .= $product?->brand?->name . ', ';
        }

        $product_name = Str::limit(rtrim($product_name, ', '), 100,);
        $product_category = Str::limit(rtrim($product_category, ', '), 100,);


        $post_data = array();
//        $post_data['store_id'] = Config::get('sslcommerz.apiCredentials.store_id');
//        $post_data['store_passwd'] = Config::get('sslcommerz.apiCredentials.store_password');
        $post_data['total_amount'] = Converter::usdTobdt($amount);
        $post_data['currency'] = $currency_code;
        $post_data['tran_id'] = $order_group_id;
        $post_data['product_category'] = $product_category;
        $post_data['emi_option'] = 0;

        # Customer Information
        $post_data['cus_name'] = $user->f_name . " " . $user->l_name;
        $post_data['cus_email'] = $user->email;
        $post_data['cus_add1'] = $shipping_address->address;
        $post_data['cus_add2'] = $shipping_address->address;
        $post_data['cus_city'] = $shipping_address->city;
        $post_data['cus_state'] = $shipping_address->state;
        $post_data['cus_postcode'] = $shipping_address->postal_code;
        $post_data['cus_country'] = $shipping_address->country;
        $post_data['cus_phone'] = $shipping_address->phone;


        #Shipment Information
        $post_data['shipping_method'] = "NO";
        $post_data['num_of_item'] = $cart->count();
        $post_data['product_name'] = $product_name;
        $post_data['product_profile'] = "general";


        $post_data['ship_name'] = $shipping_address->name;
        $post_data['ship_add1'] = $shipping_address->address;
        $post_data['ship_add2'] = $shipping_address->address;
        $post_data['ship_city'] = $shipping_address->city;
        $post_data['ship_state'] = $shipping_address->state;
        $post_data['ship_postcode'] = $shipping_address->postal_code;
        $post_data['ship_country'] = $shipping_address->country;


        try {
            $sslc = new SslCommerzPayment();
            $payment_options = $sslc->makePayment($post_data);


            $payment_options = json_decode($payment_options, true);
            if (strtolower($payment_options['status']) == 'success') {
                return [
                    'status' => true,
                    'has_redirection' => true,
                    'redirect_url' => $payment_options['data'],
                    'message' => translate('order_placed_successfully'),
                    'order_ids' => []
                ];

            } else {
                throw new \Exception($payment_options['message']);
            }
        } catch (\Exception $exception) {
            return [
                'status' => false,
                'has_redirection' => false,
                'redirect_url' => null,
                'message' => $exception->getMessage(),
                'order_ids' => []
            ];
        }
    }
}
