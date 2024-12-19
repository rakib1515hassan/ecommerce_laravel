<?php

namespace App\Http\Controllers\ProductManager;

use App\Http\Controllers\Controller;
use App\Models\AddressDistrict;
use App\Models\AddressDivision;
use App\Models\RedxProfile;
use App\Models\RedxShippingProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class RedXController extends Controller
{
    public function profile()
    {
        $redxProfile = RedxProfile::where('product_manager_id', auth('product_manager')->user()->id)->first();
        if ($redxProfile) {
            $redxParcels = RedxShippingProduct::where('redx_profile_id', $redxProfile->id)->get();
            return view('product_manager-views.redx.profile', compact('redxProfile', 'redxParcels'));
        }
        $divisions = AddressDivision::all();
        $districts = AddressDistrict::all();
        return view('product_manager-views.redx.create-profile', compact('divisions', 'districts'));
    }

    public function profileSave(Request $request)
    {
        $data = $request->validate([
            "store_name" => 'required|string',
            "devision_id" => 'required|numeric',
            "district_id" => 'required|numeric',
            "area_id" => 'required|numeric',
            "phone" => 'required|string',
            "address" => 'required|string'
        ]);

        try {
            $reduxInfo = Http::withHeaders([
                'API-ACCESS-TOKEN' => 'Bearer ' . env('REDX_ACCESS_TOKEN')
            ])
                ->post(env('REDX_BASE_URL') . "/pickup/store", [
                    'name' => $data['store_name'],
                    'address' => $data['address'],
                    'phone' => $data['phone'],
                    'area_id' => $data['area_id'],
                ])->json();

            $redxProfile = RedxProfile::where('product_manager_id', auth('product_manager')->user()->id)->first();
            $data['product_manager_id'] = auth('product_manager')->user()->id;
            $data['redx_id'] = $reduxInfo['id'];
            if (!$redxProfile) {
                $redxProfile = RedxProfile::create($data);
            }
            return back();
        } catch (\Exception $e) {
            return back()->withErrors('errors', $e->getMessage());
        }
    }
}
