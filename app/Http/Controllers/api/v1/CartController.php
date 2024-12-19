<?php

namespace App\Http\Controllers\api\v1;

use App\Models\CartShipping;
use App\Models\FlashDeal;
use App\Models\ShippingAddress;
use App\Services\CartManager;
use App\Services\AdditionalServices;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function App\Services\get_discount;
use App\Models\User;

class CartController extends Controller
{

    public function cart(Request $request)
    {
        $user = AdditionalServices::get_customer($request);
        $cart = Cart::where(['customer_id' => $user->id])->get();
        $cart->map(function ($data) {
            $data['choices'] = json_decode($data['choices']);
            $data['variations'] = json_decode($data['variations']);

            if ($data['flash_deal_id'] != null) {
                $flash_deal = FlashDeal::where('id', $data['flash_deal_id'])->first();
                if ($flash_deal == null || !$flash_deal->is_active()) {
                    $data->flash_deal_id = null;
                    $data->discount = get_discount($data->product->unit_price, $data->product->discount, $data->product->discount_type);
                    $data->save();
                }
            }
            return $data;
        });
        return response()->json($cart, 200);
    }

    public function add_to_cart(Request $request)
    {
        $user = $request->user();
        if (!User::where('id', $user->id)->first()->address->first()) {
            return response()->json(['errors' => "Please add address first."], 404);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'quantity' => 'required',
            'flash_deal_slug' => 'nullable'
        ], [
            'id.required' => translate('Product ID is required!')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)]);
        }


        $cart = CartManager::add_to_cart($request, true, $request->flash_deal_slug);
        return response()->json($cart, 200);
    }

    public function update_cart(Request $request)
    {
        $user = $request->user();
        if (!User::where('id', $user->id)->first()->address->first()) {
            return response()->json(['errors' => "Please add address first."], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'quantity' => 'required',
        ], [
            'key.required' => translate('Cart key or ID is required!')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)]);
        }

        $response = CartManager::update_cart_qty($request);
        return response()->json($response);
    }

    public function remove_from_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required'
        ], [
            'key.required' => translate('Cart key or ID is required!')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)]);
        }

        $user = AdditionalServices::get_customer($request);
        $cart = Cart::where(['id' => $request->key, 'customer_id' => $user->id])->first();
        if ($cart) {
            $cart->delete();

            CartManager::update_shipping_cost();
            return response()->json(translate('successfully_removed'));
        } else {
            return response()->json(translate('cart_not_found'));
        }
    }


    public function cart_group_shipping_cost(Request $request)
    {
        $request->validate([
            'address_id' => 'required'
        ]);

        $address = ShippingAddress::where('id', $request->address_id)->first();

        $area = $address->area;

        if ($area == null) {
            return response()->json(translate('Please select a valid shipping address!'), 400);
        }

        $carts = Cart::where(['customer_id' => $request->user()->id])->select('cart_group_id')->get()->unique('cart_group_id');

        $cart_group_ids = [];
        foreach ($carts as $cart) {
            $cart_group_ids[] = $cart->cart_group_id;
            CartManager::cart_product_shipping_cost($cart->cart_group_id, $area);
        }


        $shipping = CartShipping::whereIn('cart_group_id', $cart_group_ids)->get();

        return response()->json($shipping, 200);
    }
}
