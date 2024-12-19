<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\AddressDistrict;
use App\Models\Shop;
use App\Services\ImageManager;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function view()
    {
        $shop = Shop::where(['seller_id' => auth('seller')->id()])->first();
        if (isset($shop) == false) {
            DB::table('shops')->insert([
                'seller_id' => auth('seller')->id(),
                'name' => auth('seller')->user()->f_name,
                'address' => '',
                'contact' => auth('seller')->user()->phone,
                'image' => 'def.png',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $shop = Shop::where(['seller_id' => auth('seller')->id()])->first();
        }

        return view('seller-views.shop.shopInfo', compact('shop'));
    }

    public function edit($id)
    {
        $districts = AddressDistrict::all();
        $shop = Shop::where(['seller_id' => auth('seller')->id()])->first();
        return view('seller-views.order.edit', compact('shop', 'districts'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'address' => 'required',
            'name' => 'required',
            'contact' => 'required',
            'district_id' => 'required',
            'area_id' => 'required|exists:address_areas,id',
        ]);


        $shop = Shop::find($id);
        $shop->name = $request->name;
        $shop->address = $request->address;
        $shop->area_id = $request->area_id;
        $shop->contact = $request->contact;
        if ($request->image) {
            $shop->image = ImageManager::update('order/', $shop->image, 'png', $request->file('image'));
        }
        if ($request->banner) {
            $shop->banner = ImageManager::update('order/banner/', $shop->banner, 'png', $request->file('banner'));
        }
        $shop->save();

        Toastr::info('Shop updated successfully!');
        return redirect()->route('seller.order.view');
    }

}
