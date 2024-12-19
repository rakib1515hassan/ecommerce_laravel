<?php

namespace App\Http\Controllers\ProductManager;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use App\Services\AdditionalServices;
use App\Services\BackEndHelper;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShippingMethodController extends Controller
{
    public function index()
    {
        $shippingMethod = AdditionalServices::get_business_settings('shipping_method');

        if ($shippingMethod == 'product_managerwise_shipping') {
            $shipping_methods = ShippingMethod::where(['creator_id' => auth('product_manager')->id(), 'creator_type' => 'product_manager'])->latest()->paginate(AdditionalServices::pagination_limit());
            return view('product_manager-views.shipping-method.add-new', compact('shipping_methods'));
        } else {
            return back();
        }

    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:200',
            'duration' => 'required',
            'cost' => 'numeric'
        ]);

        DB::table('shipping_methods')->insert([
            'creator_id' => auth('product_manager')->id(),
            'creator_type' => 'product_manager',
            'title' => $request['title'],
            'duration' => $request['duration'],
            'cost' => BackEndHelper::currency_to_usd($request['cost']),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Toastr::success('Successfully added.');
        return back();
    }

    public function status_update(Request $request)
    {
        ShippingMethod::where(['id' => $request['id']])->update([
            'status' => $request['status']
        ]);
        return response()->json([
            'success' => 1,
        ], 200);
    }

    public function edit($id)
    {
        $shippingMethod = AdditionalServices::get_business_settings('shipping_method');

        if ($shippingMethod == 'product_managerwise_shipping') {
            $method = ShippingMethod::where(['id' => $id])->first();
            return view('product_manager-views.shipping-method.edit', compact('method'));
        } else {
            return redirect('/product_manager/dashboard');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:200',
            'duration' => 'required',
            'cost' => 'numeric'
        ]);

        DB::table('shipping_methods')->where(['id' => $id])->update([
            'creator_id' => auth('product_manager')->id(),
            'creator_type' => 'product_manager',
            'title' => $request['title'],
            'duration' => $request['duration'],
            'cost' => BackEndHelper::currency_to_usd($request['cost']),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Toastr::success('Successfully updated.');
        return redirect()->route('product_manager.business-settings.shipping-method.add');
    }

    public function delete(Request $request)
    {
        $shipping = ShippingMethod::find($request->id);

        $shipping->delete();
        return response()->json();
    }
}
