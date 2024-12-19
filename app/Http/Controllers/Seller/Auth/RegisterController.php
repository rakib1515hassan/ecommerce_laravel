<?php

namespace App\Http\Controllers\Seller\Auth;

use App\Models\AddressDistrict;
use App\Services\ImageManager;
use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\Shop;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function create()
    {
        $districts = AddressDistrict::all();

        return view('seller-views.auth.register', compact('districts'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|unique:sellers',
            'shop_address' => 'required',
            'f_name' => 'required',
            'l_name' => 'required',
            'shop_name' => 'required',
            'phone' => 'required',
            'password' => 'required|min:8|confirmed',
            'district_id' => 'required',
            'area_id' => 'required|exists:address_areas,id',
        ]);

        DB::transaction(function ($r) use ($request) {
            $seller = new Seller();
            $seller->f_name = $request->f_name;
            $seller->l_name = $request->l_name;
            $seller->phone = $request->phone;
            $seller->email = $request->email;
            $seller->image = ImageManager::upload('seller/', 'png', $request->file('image'));
            $seller->password = bcrypt($request->password);
            $seller->status = "pending";
            $seller->save();

            $shop = new Shop();
            $shop->seller_id = $seller->id;
            $shop->name = $request->shop_name;
            $shop->address = $request->shop_address;
            $shop->area_id = $request->area_id;
            $shop->contact = $request->phone;
            $shop->image = ImageManager::upload('order/', 'png', $request->file('logo'));
            $shop->banner = ImageManager::upload('order/banner/', 'png', $request->file('banner'));
            $shop->save();

            DB::table('seller_wallets')->insert([
                'seller_id' => $seller['id'],
                'withdrawn' => 0,
                'commission_given' => 0,
                'total_earning' => 0,
                'pending_withdraw' => 0,
                'delivery_charge_earned' => 0,
                'collected_cash' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        Toastr::success('Shop apply successfully!');
        return redirect()->route('seller.auth.login');
    }
}
