<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\DeliveryMan;
use App\Models\Order;
use App\Models\Seller;
use App\Models\ShippingAddress;
use App\Services\AdditionalServices;
use App\Services\OrderManager;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class OrderController extends Controller
{
    public function list(Request $request, $status)
    {
        $resellerId = auth('reseller')->id();
        if ($status != 'all') {
            $orders = Order::where(['reseller_id' => $resellerId])->where(['order_status' => $status]);
        } else {
            $orders = Order::where(['reseller_id' => $resellerId])->where(['reseller_id' => $resellerId]);
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
        return view('reseller-views.order.list', compact('orders', 'search'));
    }

    public function details(Request $request, $id)
    {
        $resellerId = auth('reseller')->id();
        $order = Order::with(['details', 'customer', 'shipping'])->where('id', $id)->first();

        $seller = Seller::find($order->seller_id);

        $shipping_method = AdditionalServices::get_business_settings('shipping_method');
        $delivery_men = DeliveryMan::where('is_active', 1)->when($shipping_method == 'inhouse_shipping', function ($query) {
            $query->where(['seller_id' => 0]);
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

        return view('reseller-views.order.order-details', compact('shipping_address', 'order', 'delivery_men', 'shipping_method', 'tracking', 'seller'));
    }

    public function add_delivery_man($order_id, $delivery_man_id)
    {
        if ($delivery_man_id == 0) {
            return response()->json([], 401);
        }
        $order = Order::where(['reseller_id' => auth('reseller')->id(), 'id' => $order_id])->first();
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
        } catch (\Exception $e) {
        }

        return response()->json(['status' => true], 200);
    }

    public function generate_invoice($id)
    {
        $resellerId = auth('reseller')->id();
        $order = Order::with(['details' => function ($query) use ($resellerId) {
            $query->where('reseller_id', $resellerId);
        }])->with('customer', 'shipping')
            ->with('reseller')
            ->where('id', $id)->first();

        $seller = Seller::find($resellerId)->gst;

        $order = Order::with(['details' => function ($query) use ($resellerId) {
            $query->where('reseller_id', $resellerId);
        }])->with('customer', 'shipping')
            ->with('reseller')
            ->where('id', $id)->first();

        $data["email"] = $order->customer["email"];
        $data["client_name"] = $order->customer["f_name"] . ' ' . $order->customer["l_name"];
        $data["order"] = $order;

        $mpdf_view = \View::make('reseller-views.order.invoice')->with('order', $order)->with('reseller', $seller);
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
            return response()->json(['success' => 0, 'message' => 'order is already delivered.'], 200);
        }
        $order->order_status = $request->order_status;
        OrderManager::stock_update_on_order_status_change($order, $request->order_status);

        if ($request->order_status == 'delivered' && $order['reseller_id'] != null) {
            OrderManager::wallet_manage_on_order_status_change($order, 'reseller');
        }

        $order->save();
        $data = $request->order_status;
        return response()->json($data);
    }


    public function shipping_cost()
    {

    }
}
