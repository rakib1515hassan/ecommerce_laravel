<?php

namespace App\Library\Redx;

use App\Models\AddressArea;
use App\Models\Cart;
use App\Models\Product;
use App\Models\RedxProfile;
use App\Models\RedxShippingProduct;

class RedxShippingSystem
{

    public static function getShippingCostProduct(Product $product, AddressArea $user_area, $quantity = 1): float|int
    {

        $source_zone_id = self::productShippingZoneID($product);
        $destination_zone_id = $user_area->zone->id;
        $weight = $product->weight * $quantity;

        return RedxShippingCost::getShippingCost($source_zone_id, $destination_zone_id, $weight);
    }

    private static function productShippingZoneID(Product $product)
    {
        if ($product->added_by == 'seller') {
            $seller_area = $product->seller->shop->area;
        } else {
            $seller_area = AddressArea::find(1); // todo: this is for admin products
        }

        return $seller_area?->zone->id ?? 1;
    }

    public static function getShippingCostCartGroup($cart_group_id, $user_area): float|int
    {
        $carts = Cart::where('cart_group_id', $cart_group_id)->get();

        $weight = 0;
        foreach ($carts as $cart) {
            $weight += $cart->product->weight * $cart->quantity;
        }

        $product = $carts->first()->product;

        $source_zone_id = self::productShippingZoneID($product);
        $destination_zone_id = $user_area->zone->id;

        return RedxShippingCost::getShippingCost($source_zone_id, $destination_zone_id, $weight);
    }

    public static function createShippingParcel($order)
    {
        if ($order->is_shipped) {
            throw new \Exception("Order already shipped");
        }

        $seller_is = $order->seller_is;

        $pickup_store_id = null;

        if ($seller_is == 'admin') {
            $pickup_store_id = 243599;
            $redx_profile = RedxProfile::where('redx_id', 243599)->first();
        } else {
            $redx_profile = RedxProfile::where('seller_id', $order->seller_id)->first();


            if (!$redx_profile) {
                // create redx profile
                $shop = $order->seller->shop;

                $redx_profile = RedxProfile::create([
                    'seller_id' => $order->seller_id,
                    'redx_id' => 243599,
                    'division_id' => $shop->area->district->division_id,
                    'district_id' => $shop->area->district_id,
                    'area_id' => $shop->area_id,
                    'store_name' => $shop->name,
                    'phone' => $shop->contact,
                    'address' => $shop->address,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                //{
                //    "name": "Test Pickup Store",
                //    "phone": "01898000999",
                //    "address": "Test Address",
                //    "area_id": 1
                //}

                $pickup_store = (new RedxAPI)->createPickUpStore([
                    'name' => $shop->name,
                    'phone' => $shop->contact,
                    'address' => $shop->address,
                    'area_id' => $shop->area_id
                ]);

                $redx_profile->redx_id = $pickup_store['id'];
                $redx_profile->save();
            }

            $pickup_store_id = $redx_profile->redx_id;
        }


        $order_address = json_decode($order->shipping_address_data);

        $area = AddressArea::where('id', $order_address->area_id)->first();

        if (!$area) {
            throw new \Exception("Area not found");
        }

        $products = [];
        $totalWeight = 0;
        $totalValue = 0;
        foreach ($order->details as $item) {
            $products[] = [
                'name' => json_decode($item->product_details)->name,
                'details' => json_decode($item->product_details)->details,
                'value' => (float)json_decode($item->product_details)->unit_price * (float)$item->qty,
            ];
            $totalValue += (float)json_decode($item->product_details)->unit_price * (float)$item->qty;
            $totalWeight += $item->qty * json_decode($item->product_details)->weight;
        }

        $data = [
            'customer_name' => $order_address->contact_person_name,
            "customer_phone" => $order_address->phone,
            "delivery_area" => $area->name,
            "delivery_area_id" => $area->id,
            "customer_address" => $order_address->address,
            "merchant_invoice_id" => (string)$order->id,
            "cash_collection_amount" => $order->order_amount,
            "parcel_weight" => $totalWeight,
            "instruction" => "",
            "value" => $totalValue,
            "parcel_details_json" => $products,
            "pickup_store_id" => $pickup_store_id,
        ];

//        dd($data);

        $parcel = (new RedxAPI)->createParcel($data);

//        $parcel = [
//            'tracking_id' => '1234567890'
//        ];


        RedxShippingProduct::create([
            'redx_profile_id' => $redx_profile->id,
            'tracking_id' => $parcel['tracking_id'],
            'order_id' => $order->id
        ]);

        $order->is_shipped = true;
        $order->save();

        return $parcel;
    }
}
