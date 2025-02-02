<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AdminWallet;
use App\Models\Cart;
use App\Models\CartShipping;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderTransaction;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerWallet;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;



class OrderManager
{
    public static function track_order($order_id)
    {
        $order = Order::where(['id' => $order_id])->first();
        $order['billing_address_data'] = json_decode($order['billing_address_data']);
        return $order;
    }

    public static function gen_unique_id(): string
    {
        return (string)date('Ymd') . '-' . Str::random(10);
    }

    public static function order_summary($order)
    {
        $sub_total = 0;
        $total_tax = 0;
        $total_discount_on_product = 0;
        foreach ($order->details as $key => $detail) {
            $sub_total += $detail->price * $detail->qty;
            $total_tax += $detail->tax;
            $total_discount_on_product += $detail->discount;
        }
        $total_shipping_cost = $order['shipping_cost'];
        return [
            'subtotal' => $sub_total,
            'total_tax' => $total_tax,
            'total_discount_on_product' => $total_discount_on_product,
            'total_shipping_cost' => $total_shipping_cost,
        ];
    }

    public static function stock_update_on_order_status_change($order, $status)
    {
        if ($status == 'returned' || $status == 'failed' || $status == 'canceled') {
            foreach ($order->details as $detail) {
                if ($detail['is_stock_decreased'] == 1) {
                    $product = Product::find($detail['product_id']);
                    $type = $detail['variant'];
                    $var_store = [];
                    foreach (json_decode($product['variation'], true) as $var) {
                        if ($type == $var['type']) {
                            $var['qty'] += $detail['qty'];
                        }
                        array_push($var_store, $var);
                    }
                    Product::where(['id' => $product['id']])->update([
                        'variation' => json_encode($var_store),
                        'current_stock' => $product['current_stock'] + $detail['qty'],
                    ]);
                    OrderDetail::where(['id' => $detail['id']])->update([
                        'is_stock_decreased' => 0
                    ]);
                }
            }
        } else {
            foreach ($order->details as $detail) {
                if ($detail['is_stock_decreased'] == 0) {
                    $product = Product::find($detail['product_id']);

                    //check stock
                    /*foreach ($order->details as $c) {
                        $product = Product::find($c['product_id']);
                        $type = $detail['variant'];
                        foreach (json_decode($product['variation'], true) as $var) {
                            if ($type == $var['type'] && $var['qty'] < $c['qty']) {
                                Toastr::error('Stock is insufficient!');
                                return back();
                            }
                        }
                    }*/

                    $type = $detail['variant'];
                    $var_store = [];
                    foreach (json_decode($product['variation'], true) as $var) {
                        if ($type == $var['type']) {
                            $var['qty'] -= $detail['qty'];
                        }
                        array_push($var_store, $var);
                    }
                    Product::where(['id' => $product['id']])->update([
                        'variation' => json_encode($var_store),
                        'current_stock' => $product['current_stock'] - $detail['qty'],
                    ]);
                    OrderDetail::where(['id' => $detail['id']])->update([
                        'is_stock_decreased' => 1
                    ]);
                }
            }
        }
    }

    public static function wallet_manage_on_order_status_change($order, $received_by)
    {
        $order = Order::find($order['id']);
        $order_summary = OrderManager::order_summary($order);
        $order_amount = $order_summary['subtotal'] - $order_summary['total_discount_on_product'] - $order['discount_amount'];
        $commission = AdditionalServices::sales_commission($order);
        $shipping_model = AdditionalServices::get_business_settings('shipping_method');

        if (AdminWallet::where('admin_id', 1)->first() == false) {
            DB::table('admin_wallets')->insert([
                'admin_id' => 1,
                'withdrawn' => 0,
                'commission_earned' => 0,
                'inhouse_earning' => 0,
                'delivery_charge_earned' => 0,
                'pending_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if (SellerWallet::where('seller_id', $order['seller_id'])->first() == false) {
            DB::table('seller_wallets')->insert([
                'seller_id' => $order['seller_id'],
                'withdrawn' => 0,
                'commission_given' => 0,
                'total_earning' => 0,
                'pending_withdraw' => 0,
                'delivery_charge_earned' => 0,
                'collected_cash' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($order['payment_method'] == 'cash_on_delivery') {
            DB::table('order_transactions')->insert([
                'transaction_id' => OrderManager::gen_unique_id(),
                'customer_id' => $order['customer_id'],
                'seller_id' => $order['seller_id'],
                'seller_is' => $order['seller_is'],
                'order_id' => $order['id'],
                'order_amount' => $order_amount,
                'seller_amount' => $order_amount - $commission,
                'admin_commission' => $commission,
                'received_by' => $received_by,
                'status' => 'disburse',
                'delivery_charge' => $order['shipping_cost'],
                'tax' => $order_summary['total_tax'],
                'delivered_by' => $received_by,
                'payment_method' => $order['payment_method'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $wallet = AdminWallet::where('admin_id', 1)->first();
            $wallet->commission_earned += $commission;
            if ($shipping_model == 'inhouse_shipping') {
                $wallet->delivery_charge_earned += $order['shipping_cost'];
            }
            $wallet->save();

            if ($order['seller_is'] == 'admin') {
                $wallet = AdminWallet::where('admin_id', 1)->first();
                $wallet->inhouse_earning += $order_amount;
                if ($shipping_model == 'sellerwise_shipping') {
                    $wallet->delivery_charge_earned += $order['shipping_cost'];
                }
                $wallet->total_tax_collected += $order_summary['total_tax'];
                $wallet->save();
            } else {
                $wallet = SellerWallet::where('seller_id', $order['seller_id'])->first();
                $wallet->commission_given += $commission;
                $wallet->total_tax_collected += $order_summary['total_tax'];

                if ($shipping_model == 'sellerwise_shipping') {
                    $wallet->delivery_charge_earned += $order['shipping_cost'];
                    $wallet->collected_cash += $order['order_amount']; //total order amount
                } else {
                    $wallet->total_earning += ($order_amount - $commission) + $order_summary['total_tax'];
                }

                $wallet->save();
            }
        } else {
            $transaction = OrderTransaction::where(['order_id' => $order['id']])->first();
            $transaction->status = 'disburse';
            $transaction->save();

            $wallet = AdminWallet::where('admin_id', 1)->first();
            $wallet->commission_earned += $commission;
            $wallet->pending_amount -= $order['order_amount'];
            if ($shipping_model == 'inhouse_shipping') {
                $wallet->delivery_charge_earned += $order['shipping_cost'];
            }
            $wallet->save();

            if ($order['seller_is'] == 'admin') {
                $wallet = AdminWallet::where('admin_id', 1)->first();
                $wallet->inhouse_earning += $order_amount;
                if ($shipping_model == 'sellerwise_shipping') {
                    $wallet->delivery_charge_earned += $order['shipping_cost'];
                }
                $wallet->total_tax_collected += $order_summary['total_tax'];
                $wallet->save();
            } else {
                $wallet = SellerWallet::where('seller_id', $order['seller_id'])->first();
                $wallet->commission_given += $commission;

                if ($shipping_model == 'sellerwise_shipping') {
                    $wallet->delivery_charge_earned += $order['shipping_cost'];
                    $wallet->total_earning += ($order_amount - $commission) + $order_summary['total_tax'] + $order['shipping_cost'];
                } else {
                    $wallet->total_earning += ($order_amount - $commission) + $order_summary['total_tax'];
                }

                $wallet->total_tax_collected += $order_summary['total_tax'];
                $wallet->save();
            }
        }
    }

    public static function generate_order($data)
    {
        $order_id = 100000 + Order::all()->count() + 1;

        if (Order::find($order_id)) {
            $order_id = Order::orderBy('id', 'DESC')->first()->id + 1;
        }
        $address_id = $data['request']['address_id'];
        $billing_address_id = $data['request']['address_id'];

        $coupon_code = $data['request']->has('coupon_code') ? $data['request']->has('coupon_code') : null;
        $order_note = $data['request']->has('order_note') ? $data['request']->has('order_note') : null;

        // $req = array_key_exists('request', $data) ? $data['request'] : null;
        // $discount = 0;
        // if ($req != null) {
        //     if (!$data['request']->has('coupon_code')) {
        //         $coupon_code = $req->has('x') ? $req['coupon_code'] : null;
        //         $discount = $req->has('coupon_code') ? AdditionalServices::coupon_discount($req) : 0;
        //     }
        //     if (!$data['request']->has('address_id')) {
        //         $address_id = $req->has('address_id') ? $req['address_id'] : null;
        //         $billing_address_id = $req->has('address_id') ? $req['address_id'] : null;
        //     }
        // }

        $req = array_key_exists('request', $data) ? $data['request'] : null;
        $discount = 0;
        if ($req != null) {
            $discount = $data['request']->has('coupon_code') ? AdditionalServices::coupon_discount($req) : 0;
        }

        $user = AdditionalServices::get_customer($req);

        if ($discount > 0) {
            $discount = round($discount / count(CartManager::get_cart_group_ids($req)), 2);
        }

        $cart_group_id = $data['cart_group_id'];
        $single_cart_data = Cart::where(['cart_group_id' => $cart_group_id])->first();

        $or = [
            'id' => $order_id,
            'verification_code' => rand(100000, 999999),
            'customer_id' => $user->id,
            'seller_id' => $single_cart_data->seller_id,
            'seller_is' => $single_cart_data->seller_is,
            'customer_type' => 'customer',
            'payment_status' => $data['payment_status'],
            'order_status' => $data['order_status'],
            'payment_method' => $data['payment_method'],
            'transaction_ref' => $data['transaction_ref'],
            'order_group_id' => $data['order_group_id'],
            'discount_amount' => $discount,
            'discount_type' => $discount == 0 ? null : 'coupon_discount',
            'coupon_code' => $coupon_code,
            'order_amount' => CartManager::cart_grand_total($cart_group_id) - $discount,
            'shipping_address_id' => $address_id,
            'shipping_address_data' => ShippingAddress::find($address_id),
            'billing_address' => $billing_address_id,
            'billing_address_data' => ShippingAddress::find($billing_address_id),
            'shipping_cost' => CartManager::get_shipping_cost($data['cart_group_id']),
            'shipping_method_id' => CartShipping::where(['cart_group_id' => $cart_group_id])->first()->shipping_method_id,
            'created_at' => now(),
            'updated_at' => now(),
            'order_note' => $order_note
        ];

        //Log::info('Final order amount: ', ['order_amount' => $order_amount]);

        $order_id = DB::table('orders')->insertGetId($or);

        foreach (CartManager::get_cart($data['cart_group_id']) as $c) {
            $product = Product::where(['id' => $c['product_id']])->first();
            $or_d = [
                'order_id' => $order_id,
                'product_id' => $c['product_id'],
                'seller_id' => $c['seller_id'],
                'product_details' => $product,
                'qty' => $c['quantity'],
                'price' => $c['price'],
                'tax' => $c['tax'] * $c['quantity'],
                'discount' => $c['discount'] * $c['quantity'],
                'discount_type' => 'discount_on_product',
                'variant' => $c['variant'],
                'variation' => $c['variations'],
                'delivery_status' => 'pending',
                'shipping_method_id' => null,
                'payment_status' => 'unpaid',
                'created_at' => now(),
                'updated_at' => now()
            ];

            if ($c['variant'] != null) {
                $type = $c['variant'];
                $var_store = [];
                foreach (json_decode($product['variation'], true) as $var) {
                    if ($type == $var['type']) {
                        $var['qty'] -= $c['quantity'];
                    }
                    $var_store[] = $var;
                }
                Product::where(['id' => $product['id']])->update([
                    'variation' => json_encode($var_store),
                ]);
            }

            Product::where(['id' => $product['id']])->update([
                'current_stock' => $product['current_stock'] - $c['quantity']
            ]);

            DB::table('order_details')->insert($or_d);
        }
        $order = Order::find($order_id);

//        if ($or['payment_method'] != 'cash_on_delivery') {
//            $order = Order::find($order_id);
//            $order_summary = OrderManager::order_summary($order);
//            $order_amount = $order_summary['subtotal'] - $order_summary['total_discount_on_product'] - $order['discount'];
//            $commission = AdditionalServices::sales_commission($order);
//
//            DB::table('order_transactions')->insert([
//                'transaction_id' => OrderManager::gen_unique_id(),
//                'customer_id' => $order['customer_id'],
//                'seller_id' => $order['seller_id'],
//                'seller_is' => $order['seller_is'],
//                'order_id' => $order_id,
//                'order_amount' => $order_amount,
//                'seller_amount' => $order_amount - $commission,
//                'admin_commission' => $commission,
//                'received_by' => 'admin',
//                'status' => 'hold',
//                'delivery_charge' => $order['shipping_cost'],
//                'tax' => $order_summary['total_tax'],
//                'delivered_by' => 'admin',
//                'payment_method' => $or['payment_method'],
//                'created_at' => now(),
//                'updated_at' => now(),
//            ]);
//
////            if (!AdminWallet::where('admin_id', 1)->first()) {
////                DB::table('admin_wallets')->insert([
////                    'admin_id' => 1,
////                    'withdrawn' => 0,
////                    'commission_earned' => 0,
////                    'inhouse_earning' => 0,
////                    'delivery_charge_earned' => 0,
////                    'pending_amount' => 0,
////                    'created_at' => now(),
////                    'updated_at' => now(),
////                ]);
////            }
//            DB::table('admin_wallets')->where('admin_id', $order['seller_id'])->increment('pending_amount', $order['order_amount']);
//        }

        if ($single_cart_data->seller_is == 'admin') {
            $seller = Admin::find($single_cart_data->seller_id);
        } else {
            $seller = Seller::find($single_cart_data->seller_id);
        }

        try {
            $fcm_token = $user->cm_firebase_token;
            $seller_fcm_token = $seller->cm_firebase_token;
            if ($data['payment_method'] != 'cash_on_delivery') {
                $value = AdditionalServices::order_status_update_message('confirmed');
            } else {
                $value = AdditionalServices::order_status_update_message('pending');
            }

            if ($value) {
                $data = [
                    'title' => translate('order'),
                    'description' => $value,
                    'order_id' => $order_id,
                    'image' => '',
                ];
                AdditionalServices::send_push_notif_to_device($fcm_token, $data);
                AdditionalServices::send_push_notif_to_device($seller_fcm_token, $data);
            }


            if ($order['seller_is'] == 'seller') {
                $seller = Seller::where(['id' => $single_cart_data->seller_id])->first();
            } else {
                $seller = Admin::where(['admin_role_id' => 1])->first();
            }


            if ($user->email and !app()->isLocal()) {
                Mail::to($user->email)->send(new \App\Mail\OrderPlaced($order_id));
            }
            if ($seller->email != null and !app()->isLocal()) {
                Mail::to($seller->email)->send(new \App\Mail\OrderPlaced($order_id));
            }
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }

        return $order_id;
    }
}
