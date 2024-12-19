<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Library\Redx\RedxShippingCost;
use App\Models\AddressArea;
use App\Models\AddressDistrict;
use App\Models\Order;
use App\Models\Product;
use App\Models\RedxProfile;
use App\Models\ShippingAddress;
use App\Models\User;
use App\Services\AdditionalServices;
use App\Services\ProductManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleProductController extends Controller
{
    public function search_customer()
    {
        $search = $_GET['search'];
        $customers = User::where('f_name', 'like', '%' . $search . '%')
            ->orWhere('l_name', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%')
            ->get('id', 'f_name', 'l_name', 'email', 'phone');

        return response()->json($customers);
    }


    public function create_customer(Request $request)
    {
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|unique:users,email',
            'phone' => 'required|unique:users,phone',
        ]);

        $customer = User::create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email ?? Str::random(10) . '@gmail.com',
            'phone' => $request->phone ?? Str::random(10),
            'password' => bcrypt(Str::random(12)),
        ]);


        return response()->json([
            'id' => $customer->id,
            'f_name' => $customer->f_name,
            'l_name' => $customer->l_name,
            'email' => $customer->email,
            'phone' => $customer->phone,
        ]);
    }


    public function create_shipping_address(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'contact_person_name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'area_id' => 'required',
        ]);

        $shipping_address = ShippingAddress::create([
            'customer_id' => $request->customer_id,
            'contact_person_name' => $request->contact_person_name,
            'address' => $request->address,
            'address_type' => 'shipping', // 'billing' or 'shipping
            'phone' => $request->phone,
            'area_id' => $request->area_id,
        ]);

        return response()->json([
            'id' => $shipping_address->id,
            'contact_person_name' => $shipping_address->contact_person_name,
            'address' => $shipping_address->address,
            'phone' => $shipping_address->phone,
            'area_id' => $shipping_address->area_id,
        ]);
    }


    public function get_area(Request $request)
    {
        $areas = AddressArea::where('district_id', $request->district_id)->get();
        return response()->json($areas);
    }


    public function get_shipping_address(Request $request)
    {
        $shipping_addresses = ShippingAddress::where('customer_id', $request->customer_id)->get();
        return response()->json($shipping_addresses);
    }


    public function search_product(Request $request)
    {
        $q = $request->q;

        $products = ProductManager::search_products($q, $request['limit'], $request['offset']);


        if ($products['products'] == null) {
            $products = ProductManager::translated_product_search($request['name'], $request['limit'], $request['offset']);
        }
        $products['products'] = AdditionalServices::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }

    public function create()
    {
        $customers = User::where('is_active', '1')->get();
        $districts = AddressDistrict::all();

        return view('reseller-views.sale-product.create', compact('customers', 'districts'));
    }

    public function store(Request $request)
    {

    }


    public function create_order(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'shipping_address_id' => 'required',
            'products' => 'required',
            'products.*.id' => 'required',
            'products.*.quantity' => 'required',
            'products.*.price' => 'required',
        ]);

        $reseller_id = auth('reseller')->id();
        $address = ShippingAddress::where('id', $request->shipping_address_id)->where('customer_id', $request->customer_id)->first();    // shipping address

        if (!$address) {
            return response()->json(['errors' => translate('Address not found')], 403);
        }

        // product group by seller
        $products = $request->products;

        $seller_products = [];
        foreach ($products as $product) {
            $p = Product::find($product['id']);
            if ($p) {
                $p['req'] = $product;
                $seller_id = $p->user_id;
                $added_by = $p->added_by;

                if ($added_by == 'seller' && $seller_id != null) {
                    if (!array_key_exists($seller_id, $seller_products)) {
                        $seller_products[$seller_id] = [];
                    }
                    $seller_products[$seller_id][] = $p;
                } else {
                    if (!array_key_exists('admin', $seller_products)) {
                        $seller_products[0] = [];
                    }
                    $seller_products[0][] = $p;
                }
            }
        }


        foreach ($seller_products as $seller_id => $products) {
            $order_id = 100000 + Order::all()->count() + 1;
            if (Order::find($order_id)) {
                $order_id = Order::orderBy('id', 'DESC')->first()->id + 1;
            }

            $group_id = $order_id . rand(100000, 999999);

            $total = 0;

            for ($i = 0; $i < count($products); $i++) {
                $total += $products[$i]['req']['price'] * $products[$i]['req']['quantity'];
            }

            $total_weight = 0;
            foreach ($products as $product) {
                $total_weight += $product->weight * $product['req']['quantity'];
            }

            $p = Product::find($products[0]['id']);

            $redx_seller_id = 0; // for admin
            if ($p->seller_is == 'seller') {
                $redx_seller_id = RedxProfile::where('seller_id', $p->user_id)->exists() ? $p->user_id : 0;
            }

            $redx_profile = RedxProfile::where('seller_id', $redx_seller_id)->first();

            $source_zone_id = AddressArea::find($redx_profile->area_id)->zone_id;
            $destination_area_id = AddressArea::find($address->area_id)->zone_id;

            $or = [
                'id' => $order_id,
                'verification_code' => rand(100000, 999999),
                'customer_id' => $request->customer_id,
                'seller_id' => $seller_id,
                'seller_is' => $products[0]->added_by,
                'customer_type' => 'customer',
                'payment_status' => 'unpaid',
                'order_status' => 'pending',
                'payment_method' => 'cash_on_delivery',
                'transaction_ref' => '',
                'order_group_id' => $group_id,
                'discount_amount' => 0,
                'discount_type' => 'amount',
                'coupon_code' => '',
                'order_amount' => $total,
                'shipping_address_id' => $request->shipping_address_id,
                'shipping_address_data' => ShippingAddress::find($request->shipping_address_id),
                'billing_address' => $request->shipping_address_id,
                'billing_address_data' => ShippingAddress::find($request->shipping_address_id),
                'shipping_cost' => RedxShippingCost::getShippingCost($source_zone_id, $destination_area_id, $total_weight),
                'shipping_method_id' => 'none',
                'created_at' => now(),
                'updated_at' => now(),
                'order_note' => 'Order created by reseller',
                'reseller_id' => $reseller_id,
            ];

            $order_id = DB::table('orders')->insertGetId($or);


            for ($i = 0; $i < count($products); $i++) {
                $product = $products[$i];
                $or_d = [
                    'order_id' => $order_id,
                    'product_id' => $product['id'],
                    'seller_id' => $product['user_id'],
                    'product_details' => json_encode($product),
                    'qty' => $product['req']['quantity'],
                    'price' => $product['req']['price'],
                    'tax' => 0,
                    'discount' => 0,
                    'discount_type' => 'discount_on_product',
                    'variant' => $product['req']['choice_options'],
                    'variation' => $product['variation'],
                    'delivery_status' => 'pending',
                    'shipping_method_id' => null,
                    'payment_status' => 'unpaid',
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                if ($product['req']['choice_options'] != null) {
                    $type = $product['req']['choice_options'];
                    $var_store = [];
                    foreach (json_decode($product['variation'], true) as $var) {
                        if ($type == $var['type']) {
                            $var['qty'] -= $product['req']['quantity'];
                        }
                        $var_store[] = $var;
                    }
                    Product::where(['id' => $product['id']])->update([
                        'variation' => json_encode($var_store),
                    ]);
                }

                Product::where(['id' => $product['id']])->update([
                    'current_stock' => $product['current_stock'] - $product['req']['quantity'],
                ]);

                DB::table('order_details')->insert($or_d);

            }
        }

        return response()->json(['success' => translate('Order has been placed successfully')], 200);
    }


    public function shipping_cost(Request $request)
    {
        $request->validate([
            'shipping_address_id' => 'required',
            'products' => 'required',
            'products.*.id' => 'required',
            'products.*.quantity' => 'required',
            'products.*.price' => 'required',
        ]);

        $address = ShippingAddress::find($request->shipping_address_id);    // shipping address

        $shipping_cost_data = [];

        foreach ($request->products as $product) {
            $p = Product::find($product['id']);

            if ($p) {
                $seller_id = 0; // for admin
                if ($p->seller_is == 'seller') {
                    $seller_id = $p->user_id;
                }

                // check if seller exists on shipping cost data
                if (!array_key_exists($seller_id, $shipping_cost_data)) {
                    $shipping_cost_data[$seller_id] = [
                        'weight' => 0,
                        'area_id' => 0,
                    ];
                }

                $shipping_cost_data[$seller_id]['weight'] += $p->weight * $product['quantity'];


                $redx_profile = RedxProfile::where('seller_id', $seller_id)->first();

                if (!$redx_profile) {
                    $redx_profile = RedxProfile::where('seller_id', 0)->first();
                }

                $shipping_cost_data[$seller_id]['area_id'] = $redx_profile->area_id;

            }
        }


        $shipping_cost = 0;
        $weight = 0;
        $total_seller = count($shipping_cost_data);
        foreach ($shipping_cost_data as $seller_id => $data) {
            $source_zone_id = AddressArea::find($data['area_id'])->zone_id;
            $destination_zone_id = AddressArea::find($address->area_id)->zone_id;

            $shipping_cost += RedxShippingCost::getShippingCost($source_zone_id, $destination_zone_id, $data['weight']);
            $weight += $data['weight'];
        }

        return response()->json([
            'shipping_cost' => $shipping_cost,
            'kg' => round($weight / 1000, 2),
            'total_seller' => $total_seller,
        ], 200);


    }
}
