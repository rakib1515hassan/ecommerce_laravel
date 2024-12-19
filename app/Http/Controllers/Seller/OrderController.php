<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Library\Redx\RedxAPI;
use App\Library\Redx\RedxShippingSystem;
use App\Models\DeliveryMan;
use App\Models\Order;
use App\Models\OrderTransaction;
use App\Models\RedxShippingProduct;
use App\Models\Seller;
use App\Models\ShippingAddress;
use App\Services\AdditionalServices;
use App\Services\CommissionManager;
use App\Services\OrderManager;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class OrderController extends Controller
{
    public function list(Request $request, $status)
    {
        $sellerId = auth('seller')->id();
        if ($status != 'all') {
            $orders = Order::where(['seller_is' => 'seller'])->where(['seller_id' => $sellerId])->where(['order_status' => $status]);
        } else {
            $orders = Order::where(['seller_is' => 'seller'])->where(['seller_id' => $sellerId]);
        }

        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $orders = $orders->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('id', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }
        //dd($orders->count())
        $orders = $orders->latest()->paginate(AdditionalServices::pagination_limit())->appends($query_param);
        return view('seller-views.order.list', compact('orders', 'search'));
    }

    public function details(Request $request, $id)
    {
        $sellerId = auth('seller')->id();
        $order = Order::with(['details' => function ($query) use ($sellerId) {
            $query->where('seller_id', $sellerId);
        }])->with('customer', 'shipping')
            ->where('id', $id)->first();

        $shipping_method = AdditionalServices::get_business_settings('shipping_method');
        $delivery_men = DeliveryMan::where('is_active', 1)->when($shipping_method == 'inhouse_shipping', function ($query) {
            $query->where(['seller_id' => 0]);
        })->when($shipping_method == 'sellerwise_shipping', function ($query) use ($order) {
            $query->where(['seller_id' => $order['seller_id']]);
        })->get();

        $shipping_address = ShippingAddress::find($order->shipping_address_id);
        $tracking = [];

        if ($request->tracking_id) {

            $tracking['steps'] = Http::timeout(600)->withHeaders([
                'API-ACCESS-TOKEN' => 'Bearer ' . env('REDX_ACCESS_TOKEN'),
            ])
                ->get(env('REDX_BASE_URL') . "/parcel/track/" . $request->tracking_id)->json()['tracking'];

            $tracking['tracking_id'] = $request->tracking_id;
        }

        return view('seller-views.order.order-details', compact('shipping_address', 'order', 'delivery_men', 'shipping_method', 'tracking'));
    }

    public function add_delivery_man($order_id, $delivery_man_id)
    {
        if ($delivery_man_id == 0) {
            return response()->json([], 401);
        }
        $order = Order::where(['seller_id' => auth('seller')->id(), 'id' => $order_id])->first();
        if ($order->order_status == 'delivered') {
            return response()->json(['status' => false], 200);
        }
        $order->delivery_man_id = $delivery_man_id;
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
        } catch (Exception $e) {
        }

        return response()->json(['status' => true], 200);
    }

    public function generate_invoice($id)
    {
        $sellerId = auth('seller')->id();
        $seller = Seller::find($sellerId)->gst;

        $order = Order::with(['details' => function ($query) use ($sellerId) {
            $query->where('seller_id', $sellerId);
        }])->with('customer', 'shipping')
            ->with('seller')
            ->where('id', $id)->first();

        $data["email"] = $order->customer["email"];
        $data["client_name"] = $order->customer["f_name"] . ' ' . $order->customer["l_name"];
        $data["order"] = $order;

        $mpdf_view = View::make('seller-views.order.invoice')->with('order', $order)->with('seller', $seller);
        AdditionalServices::gen_mpdf($mpdf_view, 'order_invoice_', $order->id);
    }

    public function payment_status(Request $request)
    {
        if ($request->ajax()) {
            $order = Order::find($request->id);
            $order->payment_status = $request->payment_status;
            $order->save();
            $data = $request->payment_status;
            return response()->json($data);
        }
    }

    public function status(Request $request)
    {
        $order = Order::find($request->id);
        $fcm_token = $order->customer->cm_firebase_token;

        $value = AdditionalServices::order_status_update_message($request->order_status);
        try {
            if ($value) {
                $data = [
                    'title' => translate('Order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                AdditionalServices::send_push_notif_to_device($fcm_token, $data);
            }
        } catch (Exception $e) {
        }

        $order->order_status = $request->order_status;

        if ($request->order_status == 'shipped' && $order->is_shipped == false) {
            RedxShippingSystem::createShippingParcel($order);
            $order->is_shipped = true;
            $order->save();
        }


        OrderManager::stock_update_on_order_status_change($order, $request->order_status);

        if ($request->order_status == 'delivered' && $order['seller_id'] != null) {

            if ($order->is_shipped == false) {
                return response()->json(['status' => false], 400);
            }

            //OrderManager::wallet_manage_on_order_status_change($order, 'admin');
            CommissionManager::set_commission($order);
            CommissionManager::customer_point_calculations($order);
            $order->is_delivered = 1;
            $order->save();
        }

        $transaction = OrderTransaction::where(['order_id' => $order['id']])->first();
        if (isset($transaction) && $transaction['status'] == 'disburse') {
            return response()->json($request->order_status);
        }

        $order->save();

        return response()->json($request->order_status);

    }


    public function track_order($id)
    {
        $tracking_id = RedxShippingProduct::where('order_id', $id)->first();

        if ($tracking_id) {
            $tracking_id = $tracking_id->tracking_id;

            $tracking_data = (new RedxAPI())->trackParcel($tracking_id);

            return response()->json([
                'status' => true,
                'data' => $tracking_data
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Tracking ID not found'
            ], 400);
        }
    }
}
