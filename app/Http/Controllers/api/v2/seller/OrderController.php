<?php

namespace App\Http\Controllers\api\v2\seller;

use App\Services\AdditionalServices;
use App\Services\OrderManager;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminWallet;
use App\Models\BusinessSetting;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\SellerWallet;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;



class OrderController extends Controller
{
    public function list(Request $request)
    {
        $data = AdditionalServices::get_seller_by_token($request);

        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        $order_ids = OrderDetail::where(['seller_id' => $seller['id']])->pluck('order_id')->toArray();
        $orders = Order::with(['customer'])->whereIn('id', $order_ids)->get();
        $orders->map(function ($data) {
            $data['billing_address_data'] = json_decode($data['billing_address_data']);
            return $data;
        });

        return response()->json($orders, 200);
    }

    public function details(Request $request, $id)
    {
        $data = AdditionalServices::get_seller_by_token($request);

        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        $details = OrderDetail::where(['seller_id' => $seller['id'], 'order_id' => $id])->get();
        foreach ($details as $det) {
            $det['product_details'] = AdditionalServices::product_data_formatting(json_decode($det['product_details'], true));
        }

        return response()->json($details, 200);
    }

    public function assign_delivery_man(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'delivery_man_id' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)]);
        }

        $data = AdditionalServices::get_seller_by_token($request);

        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        $order = Order::where(['seller_id' => $seller['id'], 'id' => $request['order_id']])->first();
        if ($order->order_status == 'delivered') {
            return response()->json(['status' => false], 200);
        }
        $order->delivery_man_id = $request['delivery_man_id'];
        $order->save();

        $fcm_token = $order->delivery_man->fcm_token;
        $value = AdditionalServices::order_status_update_message('del_assign');
        try {
            if ($value) {
                $data = [
                    'title' => translate('order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                AdditionalServices::send_push_notif_to_device($fcm_token, $data);
            }
        } catch (\Exception $e) {
        }

        return response()->json(['success' => 1, 'message' => translate('order_deliveryman_assigned_successfully')], 200);
    }

    public function order_detail_status(Request $request)
    {
        $data = AdditionalServices::get_seller_by_token($request);

        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => translate('Your existing session token does not authorize you any more')
            ], 401);
        }

        $order = Order::find($request->id);

        try {
            $fcm_token = $order->customer->cm_firebase_token;
            $value = AdditionalServices::order_status_update_message($request->order_status);
            if ($value) {
                $notif = [
                    'title' => translate('Order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                AdditionalServices::send_push_notif_to_device($fcm_token, $notif);
            }
        } catch (\Exception $e) {
            return response()->json([]);
        }

        try {
            $fcm_token_delivery_man = $order->delivery_man->fcm_token;
            if ($value != null) {
                $data = [
                    'title' => translate('order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                AdditionalServices::send_push_notif_to_device($fcm_token_delivery_man, $data);
            }
        } catch (\Exception $e) {
        }

        if ($order->order_status == 'delivered') {
            return response()->json(['success' => 0, 'message' => translate('order is already delivered')], 200);
        }
        $order->order_status = $request->order_status;
        OrderManager::stock_update_on_order_status_change($order, $request->order_status);

        if ($request->order_status == 'delivered' && $order['seller_id'] != null) {
            OrderManager::wallet_manage_on_order_status_change($order, 'seller');
        }

        $order->save();

        return response()->json(['success' => 1, 'message' => translate('order_status_updated_successfully')], 200);
    }
}
