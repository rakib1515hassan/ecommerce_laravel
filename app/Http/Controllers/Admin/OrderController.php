<?php

namespace App\Http\Controllers\Admin;

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
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use App\Services\SmsModule;


// For CSV File Download
use League\Csv\Writer;
use SplTempFileObject;

class OrderController extends Controller
{
    // public function list(Request $request, $status)
    // {
    //     $query_param = [];
    //     $search = $request->input('search');
    //     $from_date = $request->input('from_date');
    //     $to_date = $request->input('to_date');

    //     // Initialize the query based on in-house orders or not
    //     if (session()->has('show_inhouse_orders') && session('show_inhouse_orders') == 1) {
    //         $query = Order::whereHas('details', function ($query) {
    //             $query->whereHas('product', function ($query) {
    //                 $query->where('added_by', 'admin');
    //             });
    //         })->with(['customer']);
    //     } else {
    //         $query = Order::with(['customer']);
    //     }

    //     // Filter by status if provided
    //     if ($status != 'all') {
    //         $query = $query->where(['order_status' => $status]);
    //     }

    //     // Apply search filter if provided
    //     if ($request->has('search')) {
    //         $key = explode(' ', $request->input('search'));
    //         $query = $query->where(function ($q) use ($key) {
    //             foreach ($key as $value) {
    //                 $q->orWhere('id', 'like', "%{$value}%")
    //                     ->orWhere('order_status', 'like', "%{$value}%")
    //                     ->orWhere('transaction_ref', 'like', "%{$value}%");
    //             }
    //         });
    //         $query_param['search'] = $request->input('search');
    //     }

    //     // Apply date filter if provided
    //     if ($from_date && $to_date) {
    //         $query = $query->whereBetween('created_at', [$from_date, $to_date]);
    //         $query_param['from_date'] = $from_date;
    //         $query_param['to_date'] = $to_date;
    //     }

    //     // Check if the request is for CSV download
    //     if ($request->has('download') && $request->input('download') === 'csv') {
    //         $orders = $query->get(); // Get all records for CSV
    //         return $this->downloadCsv($orders);
    //     }

    //     // Paginate for displaying in the view
    //     $orders = $query->orderBy('id', 'desc')
    //         ->paginate(AdditionalServices::pagination_limit())
    //         ->appends($query_param);

    //     return view('admin-views.order.list', compact('orders', 'search', 'status'));
    // }



    public function list(Request $request, $status)
    {
        $query_param = [];
        $search = $request->input('search');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        // Initialize the query based on in-house orders or not
        if (session()->has('show_inhouse_orders') && session('show_inhouse_orders') == 1) {
            $query = Order::whereHas('details', function ($query) {
                $query->whereHas('product', function ($query) {
                    $query->where('added_by', 'admin');
                });
            })->with(['customer']);
        } else {
            $query = Order::with(['customer']);
        }

        // Filter by status if provided
        if ($status != 'all') {
            $query = $query->where(['order_status' => $status]);
        }

        // Apply search filter if provided
        if ($request->has('search')) {
            $key = explode(' ', $request->input('search'));
            $query = $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_ref', 'like', "%{$value}%");
                }
            });
            $query_param['search'] = $request->input('search');
        }

        // Apply date filter if provided
        if ($from_date && $to_date) {
            $query = $query->whereBetween('created_at', [$from_date, $to_date]);
            $query_param['from_date'] = $from_date;
            $query_param['to_date'] = $to_date;
        }

        // Check if the request is for CSV download
        if ($request->has('download')) {
            $orders = $query->get(); // Get all filtered records for CSV
            return $this->downloadCsv($orders);
        }

        // Paginate for displaying in the view
        $orders = $query->orderBy('id', 'desc')
            ->paginate(AdditionalServices::pagination_limit())
            ->appends($query_param);

        return view('admin-views.order.list', compact('orders', 'search', 'status'));
    }







    private function downloadCsv($orders)
    {
        // Create CSV file
        $csv = Writer::createFromFileObject(new SplTempFileObject());

        // Insert header
        $csv->insertOne([
            'Order ID',
            'Customer Name',
            'Customer Email',
            'Customer Phone',

            'Shipping Address',
            'Shipping City',
            'Shipping Zip',
            'Shipping Phone',

            'Order Status',
            'Payment Status',
            'Payment Method',
            'Transaction Reference',
            'Order Amount',
            'Discount Amount',
            'Created At'
        ]);

        // Insert rows
        foreach ($orders as $order) {

            $shippingAddress = json_decode($order->shipping_address_data, true);

            $address = $shippingAddress['address'] ?? 'N/A';
            $city = $shippingAddress['city'] ?? 'N/A';
            $zip = $shippingAddress['zip'] ?? 'N/A';
            $phone = strval($shippingAddress['phone'] ?? 'N/A');

            $csv->insertOne([
                $order->id,
                $order->customer ? $order->customer->fullname : 'N/A',
                $order->customer ? $order->customer->email : 'N/A',
                strval($order->customer ? $order->customer->phone : 'N/A'),

                $address,
                $city,
                $zip,
                $phone,

                $order->order_status,
                $order->payment_status,
                $order->payment_method,
                $order->transaction_ref,

                $order->order_amount,
                $order->discount_amount,

                $order->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        // Return CSV file as response
        $csv->output('orders.csv');
        exit;
    }









    public function details($id)
    {
        $order = Order::with('details', 'shipping', 'seller')->where(['id' => $id])->first();
        $linked_orders = Order::where(['order_group_id' => $order['order_group_id']])
            ->whereNotIn('order_group_id', ['def-order-group'])
            ->whereNotIn('id', [$order['id']])
            ->get();

        $shipping_method = AdditionalServices::get_business_settings('shipping_method');
        $delivery_men = DeliveryMan::where('is_active', 1)->when($order->seller_is == 'admin', function ($query) {
            $query->where(['seller_id' => 0]);
        })->when($order->seller_is == 'seller' && $shipping_method == 'sellerwise_shipping', function ($query) use ($order) {
            $query->where(['seller_id' => $order['seller_id']]);
        })->when($order->seller_is == 'seller' && $shipping_method == 'inhouse_shipping', function ($query) use ($order) {
            $query->where(['seller_id' => 0]);
        })->get();

        $shipping_address = ShippingAddress::find($order->shipping_address_id);

        return view('admin-views.order.order-details', compact('shipping_address', 'order', 'linked_orders', 'delivery_men'));
    }



    public function add_delivery_man($order_id, $delivery_man_id)
    {
        if ($delivery_man_id == 0) {
            return response()->json([], 401);
        }
        $order = Order::find($order_id);
        /*if($order->order_status == 'delivered' || $order->order_status == 'returned' || $order->order_status == 'failed' || $order->order_status == 'canceled' || $order->order_status == 'scheduled') {
            return response()->json(['status' => false], 200);
        }*/
        $order->delivery_man_id = $delivery_man_id;
        $order->save();

        $fcm_token = $order->delivery_man->fcm_token;
        $value = AdditionalServices::order_status_update_message('del_assign') . " ID: " . $order['id'];
        try {
            if ($value != null) {
                $data = [
                    'title' => translate('order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                AdditionalServices::send_push_notif_to_device($fcm_token, $data);
            }
        } catch (\Exception $e) {
            Toastr::warning(translate('Push notification failed for DeliveryMan!'));
        }

        return response()->json(['status' => true], 200);
    }

    /**
     * @throws \Exception
     */
    public function status(Request $request)
    {
        // Log::info("Status Got = " . $request->order_status);
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
        }
        
        $order->order_status = $request->order_status;
        
        // if ($request->order_status == 'shipped' && $order->is_shipped == false) {
            //     RedxShippingSystem::createShippingParcel($order);
            //     $order->is_shipped = true;
            //     $order->save();
            // }
            
            
        OrderManager::stock_update_on_order_status_change($order, $request->order_status);
        
        if ($request->order_status == 'delivered') {
            if ($request->order_status == 'delivered' && $order['seller_id'] != null) {
                // if ($order->is_shipped == false) {
                //     return response()->json(['status' => false], 400);
                // }
                //OrderManager::wallet_manage_on_order_status_change($order, 'admin');
                CommissionManager::set_commission($order);
            }

            $order->is_delivered = 1;
            $order->save();

            $user = $order->customer;
            Log::info('SMS Response = ' . $user->phone);
            if ($user->phone) {
                $msg = "আপনার অর্ডারটি ডেলিভারি সম্পন্ন হয়েছে। \nপণ্যটি সঠিকভাবে পেয়েছেন কিনা? \nযেকোন অভিযোগ জানাতে কল করুন আমাদের হট লাইনে। \nধন্যবাদ, \nআমাদের সাথে থাকার জন্যে \nShojonsl.com";
                $res = SmsModule::sendSms_greenweb($user, $msg);
                Log::info('SMS Response = ' . $res);
            }
        }


        $transaction = OrderTransaction::where(['order_id' => $order['id']])->first();
        if (isset($transaction) && $transaction['status'] == 'disburse') {
            return response()->json($request->order_status);
        }

        $order->save();

        return response()->json($request->order_status);
    }

    public function payment_status(Request $request)
    {
        if ($request->ajax()) {
            $order = Order::find($request->id);
            $order->payment_status = $request->payment_status;
            $order->save();
            $data = $request->payment_status;

            // Point calculation
            if ($request->payment_status == 'paid') {
                Log::info("Status Got Paid");
                // CommissionManager::customer_point_calculations($id = $request->id);
                CommissionManager::customer_point_calculations($order);
            }

            return response()->json($data);
        }
    }

    public function generate_invoice($id)
    {
        $order = Order::with('seller')->with('shipping')->with('details')->where('id', $id)->first();
        $seller = Seller::find($order->details->first()->seller_id);
        $data["email"] = $order->customer["email"];
        $data["client_name"] = $order->customer["f_name"] . ' ' . $order->customer["l_name"];
        $data["order"] = $order;

        $mpdf_view = View::make('admin-views.order.invoice')->with('order', $order)->with('seller', $seller);
        AdditionalServices::gen_mpdf($mpdf_view, 'order_invoice_', $order->id);
    }



    public function inhouse_order_filter()
    {
        if (session()->has('show_inhouse_orders') && session('show_inhouse_orders') == 1) {
            session()->put('show_inhouse_orders', 0);
        } else {
            session()->put('show_inhouse_orders', 1);
        }
        return back();
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
