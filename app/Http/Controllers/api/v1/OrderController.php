<?php

namespace App\Http\Controllers\api\v1;

use App\Models\Cart;
use App\Services\CartManager;
use App\Services\AdditionalServices;
use App\Services\OrderManager;
use App\Services\PaymentService;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Coupon;
use Illuminate\Support\Facades\Log;
use App\Services\SmsModule;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function track_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        return response()->json(OrderManager::track_order($request['order_id']), 200);
    }

    // public function place_order(Request $request)
    // {

    //     Log::info('Place order request received', ['request_data' => $request->all()]);

    //     $validator = Validator::make($request->all(), [
    //         'address_id' => 'required',
    //         'payment_method' => 'required|in:cash_on_delivery,ssl_commerz_payment',
    //         'order_note' => 'nullable',
    //         'coupon_code' => 'nullable|exists:coupons,code',
    //     ]);

    //     // $user = $request->user();
    //     // $user_coupon = Coupon::where('user_id', $user->id)
    //     //     ->where('code', $request->input('coupon_code'))
    //     //     ->first();

    //     // if (!$user_coupon) {
    //     //     return response()->json(['error' => 'Invalid Coupon!'], 401);
    //     // }

    //     $address = ShippingAddress::where(['customer_id' => $request->user()->id, 'id' => $request['address_id']])->first();
    //     $carts = Cart::where('customer_id', auth()->id())->get();

    //     if (!$address) {
    //         $validator->errors()->add('address_id', translate('Address not found'));
    //         return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
    //     }

    //     if (count($carts) == 0) {
    //         $validator->errors()->add('address_id', translate('Cart is empty'));
    //         return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
    //     }

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
    //     }

    //     $request['billing_address_id'] = $request['address_id'];

    //     $payment_service = new PaymentService();

    //     if ($request['payment_method'] == 'cash_on_delivery') {
    //         Log::info('Processing cash on delivery payment');
    //         $response = $payment_service->cod_payment($request);
    //     } elseif ($request['payment_method'] == 'ssl_commerz_payment') {
    //         $response = $payment_service->ssl_commerz_payment($request);
    //     } else {
    //         return response()->json(['errors' => translate('payment_method_not_found')], 403);
    //     }

    //     Log::info('Order placed successfully', ['response' => $response]);
    //     return response()->json($response, 200);
    // }

    public function place_order(Request $request)
    {
        Log::info('Order placed');

        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
            'payment_method' => 'required|in:cash_on_delivery,ssl_commerz_payment',
            'order_note' => 'nullable',
            'coupon_code' => 'nullable|exists:coupons,code',
        ]);


        if ($request->has('coupon_code')) {
            $user = $request->user();
            $coupon = Coupon::
                where('code', $request->input('coupon_code'))
                ->first();

            if (!$coupon) {
                return response()->json(['error' => 'Invalid Coupon!'], 401);
            }

            if ($coupon->limit <= 0) {
                return response()->json(['error' => 'Coupon limit reached!'], 401);
            }
            $coupon->limit -= 1;
            $coupon->update();

            if ($coupon->limit == 0) {
                $coupon->status = 0;
                $coupon->update();
            }


        }

        if (isset($coupon)) {
            Log::info('Coupon fetched successfully: ', ['coupon' => $coupon]);
        } else {
            Log::info('Coupon fetch failed or coupon invalid.');
        }

        $address = ShippingAddress::where(['customer_id' => $request->user()->id, 'id' => $request['address_id']])->first();
        $carts = Cart::where('customer_id', auth()->id())->get();

        if (!$address) {
            $validator->errors()->add('address_id', translate('Address not found'));
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        if (count($carts) == 0) {
            $validator->errors()->add('address_id', translate('Cart is empty'));
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $request['billing_address_id'] = $request['address_id'];

        $payment_service = new PaymentService();

        if ($request['payment_method'] == 'cash_on_delivery') {
            $response = $payment_service->cod_payment($request);
        } elseif ($request['payment_method'] == 'ssl_commerz_payment') {
            $response = $payment_service->ssl_commerz_payment($request);
        } else {
            return response()->json(['errors' => translate('payment_method_not_found')], 403);
        }


        // Send SMS after order placement
        $user = $request->user();
        
        if ($user->phone){
            $msg = "আপনার অর্ডারটি গ্রহন করা হয়েছে, \n ধন্যবাদ, \n আমাদের সাথে থাকার জন্যে \n Shojonsl.com";

            $res = SmsModule::sendSms_greenweb($user, $msg);
            Log::info('SMS Response = ' . $res);
        }

        return response()->json($response, 200);
    }

}
