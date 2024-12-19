<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\AddressArea;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Models\SupportTicket;
use App\Models\SupportTicketConv;
use App\Models\User;
use App\Models\Wishlist;
use App\Services\AdditionalServices;
use App\Services\CustomerManager;
use App\Services\ImageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function info(Request $request)
    {
        return response()->json($request->user(), 200);
    }

    public function create_support_ticket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required',
            'type' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $request['customer_id'] = $request->user()->id;
        $request['priority'] = 'low';
        $request['status'] = 'pending';

        try {
            CustomerManager::create_support_ticket($request);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'code' => 'failed',
                    'message' => 'Something went wrong',
                ],
            ], 422);
        }
        return response()->json(['message' => 'Support ticket created successfully.'], 200);
    }

    public function reply_support_ticket(Request $request, $ticket_id)
    {
        $support = new SupportTicketConv();
        $support->support_ticket_id = $ticket_id;
        $support->admin_id = 1;
        $support->customer_message = $request['message'];
        $support->save();
        return response()->json(['message' => 'Support ticket reply sent.'], 200);
    }

    public function get_support_tickets(Request $request)
    {
        return response()->json(SupportTicket::where('customer_id', $request->user()->id)->get(), 200);
    }

    public function get_support_ticket_conv($ticket_id)
    {
        return response()->json(SupportTicketConv::where('support_ticket_id', $ticket_id)->get(), 200);
    }

    public function add_to_wishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $wishlist = Wishlist::where('customer_id', $request->user()->id)->where('product_id', $request->product_id)->first();

        if (empty($wishlist)) {
            $wishlist = new Wishlist;
            $wishlist->customer_id = $request->user()->id;
            $wishlist->product_id = $request->product_id;
            $wishlist->save();
            return response()->json(['message' => translate('successfully added!')], 200);
        }

        return response()->json(['message' => translate('Already in your wishlist')], 200);
    }

    public function remove_from_wishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $wishlist = Wishlist::where('customer_id', $request->user()->id)->where('product_id', $request->product_id)->first();

        if (!empty($wishlist)) {
            Wishlist::where(['customer_id' => $request->user()->id, 'product_id' => $request->product_id])->delete();
            return response()->json(['message' => translate('successfully removed!')], 200);

        }
        return response()->json(['message' => translate('No such data found!')], 404);
    }

    public function wish_list(Request $request)
    {
        $wishlists = Wishlist::whereHas('product')->where('customer_id', $request->user()->id)->get();

        $product_ids = array();

        foreach ($wishlists as $key => $wishlist) {
            $product_ids[] = $wishlist->product_id;
        }

        $products = Product::whereIn('id', $product_ids)->get();


        return response()->json(AdditionalServices::product_data_formatting($products, true), 200);
    }

    public function address_list(Request $request)
    {
        $shippingAddress = ShippingAddress::where('customer_id', $request->user()->id)->get();

        foreach ($shippingAddress as $key => $address) {
            $address->area?->district;
        }

        return response()->json($shippingAddress, 200);
    }

    public function add_new_address(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'address' => 'required',
            'area_id' => 'required|exists:address_areas,id',
            'zip' => 'nullable',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $address = [
            'customer_id' => $request->user()->id,
            'contact_person_name' => $request->contact_person_name,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'area_id' => $request->area_id,
            'zip' => $request->zip ?? 1200,
            'phone' => $request->phone,
            'latitude' => $request->latitude ?? 90,
            'longitude' => $request->longitude ?? 24,
            'is_billing' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('shipping_addresses')->insert($address);
        return response()->json(['message' => translate('successfully added!')], 200);
    }

    public function edit_address(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_addresses_id' => 'required|exists:shipping_addresses,id',
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'address' => 'required',
            'area_id' => 'required|exists:address_areas,id',
            'zip' => 'nullable',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $address_id = $request->shipping_addresses_id;

        if (ShippingAddress::where('id', $address_id)->where('customer_id', $request->user()->id)->exists()) {
            $address = [
                'contact_person_name' => $request->contact_person_name,
                'address_type' => $request->address_type,
                'address' => $request->address,
                'area_id' => $request->area_id,
                'zip' => $request->zip ?? 1200,
                'phone' => $request->phone,
                'latitude' => $request->latitude ?? 90,
                'longitude' => $request->longitude ?? 24,
                'is_billing' => false,
                'updated_at' => now(),
            ];
            DB::table('shipping_addresses')->where('id', $address_id)->update($address);
            return response()->json(['message' => translate('successfully updated!')], 200);
        }
        return response()->json(['message' => translate('No such data found!')], 404);
    }

    public function delete_address(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        if (DB::table('shipping_addresses')->where(['id' => $request['address_id'], 'customer_id' => $request->user()->id])->first()) {
            DB::table('shipping_addresses')->where(['id' => $request['address_id'], 'customer_id' => $request->user()->id])->delete();
            return response()->json(['message' => 'successfully removed!'], 200);
        }
        return response()->json(['message' => translate('No such data found!')], 404);
    }

    public function get_order_list(Request $request)
    {
        $orders = Order::where(['customer_id' => $request->user()->id])->get();
        $orders->map(function ($data) {
            $data['shipping_address_data'] = json_decode($data['shipping_address_data']);
            //$data['billing_address_data'] = json_decode($data['billing_address_data']);
            return $data;
        });
        return response()->json($orders, 200);
    }

    public function get_order_list_with_details(Request $request)
    {
        $id = Auth::id();
        $orders = Order::with('details')->where(['customer_id' => $id])->orderBy('created_at', 'desc')->paginate();

        $orders->map(function ($data) {
            $data['shipping_address_data'] = json_decode($data['shipping_address_data'], true);

            $area = AddressArea::with(['district', 'district.division'])->where('id', $data['shipping_address_data']['area_id'])->first();
            $data['shipping_address_data'] = array_merge($data['shipping_address_data'], [
                'city' => $area->name,
                'area' => $area->name,
                'district' => $area->district->name,
                'division' => $area->district->division->name
            ]);

            unset($data['billing_id']);
            unset($data['billing_address_data']);

            $data->details->map(function ($data) {
                $data['product_details'] = collect(json_decode($data['product_details'], true))->only([
                    'id', 'slug', 'name', 'thumbnail',
                ]);
                $data['variation'] = json_decode($data['variation'], true);
                return $data;
            });

            return $data;
        });

        return response()->json($orders, 200);

    }

    public function get_order_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $details = OrderDetail::where(['order_id' => $request['order_id']])->get();
        $details->map(function ($query) {
            $query['variation'] = json_decode($query['variation'], true);
            $query['product_details'] = AdditionalServices::product_data_formatting(json_decode($query['product_details'], true));
            return $query;
        });

        $order = Order::where(['id' => $request['order_id']])->first();
        $order['product_details'] = $details;


        return response()->json($order, 200);
    }

    public function cancel_order(Request $request)
    {
        // Log::info("Got Cancel Order");
        // Log::info('Request data:', $request->all());

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $order = Order::where(['id' => $request['order_id'], 'customer_id' => $request->user()->id])->first();

        // Log::info('Order =:'. $order);

        if ($order) {
            if ($order && $order->order_status == 'pending') {
                $order->update(['order_status' => 'cancelled']);
            }
            else {
                return response()->json([
                    'error' => "Order is already confirmed, You can't cancel it."
                ], 422);
            }

            return response()->json([
                'status' => true,
                'message' => translate('Order cancelled successfully!')
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => translate('No such data found!')
        ], 404);

        // return response()->json(["Cancelled"]);
    }

    public function update_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
            'phone' => 'required',
        ], [
            'f_name.required' => translate('First name is required!'),
            'l_name.required' => translate('Last name is required!'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        if ($request->has('image')) {
            $imageName = ImageManager::update('profile/', $request->user()->image, 'png', $request->file('image'));
        } else {
            $imageName = $request->user()->image;
        }

        if ($request['password'] != null && strlen($request['password']) > 5) {
            $pass = bcrypt($request['password']);
        } else {
            $pass = $request->user()->password;
        }

        $userDetails = [
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'phone' => $request->phone,
            'image' => $imageName,
            'password' => $pass,
            'updated_at' => now(),
        ];

        User::where(['id' => $request->user()->id])->update($userDetails);

        return response()->json(['message' => translate('successfully updated!')], 200);
    }

    public function update_cm_firebase_token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cm_firebase_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        DB::table('users')->where('id', $request->user()->id)->update([
            'cm_firebase_token' => $request['cm_firebase_token'],
        ]);

        return response()->json(['message' => translate('successfully updated!')], 200);
    }
}
