<?php

namespace App\Http\Controllers\api\v1;

use App\Services\CartManager;
use App\Services\AdditionalServices;
use App\Http\Controllers\Controller;
use App\Models\CartShipping;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ShippingMethodController extends Controller
{
    public function get_shipping_method_info($id)
    {
        try {
            $shipping = ShippingMethod::find($id);
            return response()->json($shipping, 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    public function shipping_methods_by_seller($id, $seller_is)
    {
        $seller_is = $seller_is == 'admin' ? 'admin' : 'seller';
        return response()->json(AdditionalServices::get_shipping_methods($id, $seller_is), 200);
    }

    public function choose_for_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_group_id' => 'required',
            'id' => 'required'
        ], [
            'id.required' => translate('shipping_id_is_required')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)]);
        }

        if ($request['cart_group_id'] == 'all_cart_group') {
            foreach (CartManager::get_cart_group_ids($request) as $group_id) {
                $request['cart_group_id'] = $group_id;
                self::insert_into_cart_shipping($request);
            }
        } else {
            self::insert_into_cart_shipping($request);
        }

        return response()->json(translate('successfully_added'));
    }

    public static function insert_into_cart_shipping($request)
    {
        //dd(ShippingMethod::all());
        $shipping = CartShipping::where(['cart_group_id' => $request['cart_group_id']])->first();
        if (!isset($shipping)) {
            $shipping = new CartShipping();
        }
        $shipping['cart_group_id'] = $request['cart_group_id'];
        $shipping['shipping_method_id'] = $request['id'];
        $shipping['shipping_cost'] = ShippingMethod::find($request['id'])->cost;
        $shipping->save();
    }

    public function chosen_shipping_methods(Request $request)
    {
        $group_ids = CartManager::get_cart_group_ids($request);
        return response()->json(CartShipping::whereIn('cart_group_id', $group_ids)->get(), 200);
    }
}
