<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\AddressDistrict;
use App\Models\AddressDivision;
use App\Models\RedxProfile;
use App\Models\RedxShippingProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RedXController extends Controller
{
    public function profile()
    {
        $redxProfile = RedxProfile::where('seller_id', auth('seller')->user()->id)->first();
        if ($redxProfile) {
            $redxParcels = RedxShippingProduct::where('redx_profile_id', $redxProfile->id)->get();
            return view('seller-views.redx.profile', compact('redxProfile', 'redxParcels'));
        }
        $divisions = AddressDivision::all();
        $districts = AddressDistrict::all();
        return view('seller-views.redx.create-profile', compact('divisions', 'districts'));
    }

    public function profileSave(Request $request)
    {
        $data = $request->validate([
            "store_name" => 'required|string',
            "division_id" => 'required|numeric',
            "district_id" => 'required|numeric',
            "area_id" => 'required|numeric',
            "phone" => 'required|string',
            "address" => 'required|string'
        ]);

        try {
            $redx_base = env('REDX_BASE_URL');
            $redx_access_token = env('REDX_ACCESS_TOKEN');

            $reduxInfo = Http::withHeaders([
                'API-ACCESS-TOKEN' => 'Bearer ' . $redx_access_token,
            ])->post($redx_base . "/pickup/store", [
                'name' => $data['store_name'],
                'address' => $data['address'],
                'phone' => $data['phone'],
                'area_id' => $data['area_id'],
            ])->json();

            $redxProfile = RedxProfile::where('seller_id', auth('seller')->user()->id)->first();
            $data['seller_id'] = auth('seller')->user()->id;
            $data['redx_id'] = $reduxInfo['id'];

            if (!$redxProfile) {
                RedxProfile::create($data);
            }

            return back();
        } catch (\Exception $e) {
            return back()->withErrors('errors', $e->getMessage());
        }
    }
}