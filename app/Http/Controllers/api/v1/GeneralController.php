<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\AddressArea;
use App\Models\AddressDistrict;
use App\Models\BusinessSetting;
use App\Models\HelpTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GeneralController extends Controller
{
    public function faq()
    {
        return response()->json(HelpTopic::orderBy('ranking')->get(), 200);
    }

    public function helper_page($slug, Request $request)
    {
        if (!in_array($slug, ['about_us', 'terms_and_conditions', 'privacy_policy', 'contact_us', 'return_and_refunds', 'future_plans'])) {
            abort(404);
        }

        $data = BusinessSetting::where('type', $request->slug)->first();

        return response()->json([
            'title' => Str::title(str_replace('_', ' ', $request->slug)),
            'content' => $data->value
        ]);
    }


    public function address(Request $request)
    {
        $district_id = $request->district_id;

        if ($request->has('district_id')) {
            $address = AddressArea::where('district_id', $district_id)->get()->map(function ($address) {
                return [
                    'id' => $address->id,
                    'name' => $address->name,
                    'type' => 'area'
                ];
            });
        } else if ($request->has('area_id')) {
            $area_id = $request->area_id;

            $address = AddressArea::with(['district', 'division'])->find($area_id);

            $address = [
                'id' => $address->id,
                'name' => $address->name,
                'type' => 'area',
                'district' => [
                    'id' => $address->district->id,
                    'name' => $address->district->name,
                    'type' => 'district',
                    'division' => [
                        'id' => $address->division->id,
                        'name' => $address->division->name,
                        'type' => 'division'
                    ]
                ]
            ];

            return response()->json($address, 200);
        } else if ($request->has('division_id')) {
            return AddressDistrict::where('division_id', $request->division_id)->get();
        } else {
            $address = AddressDistrict::all()->map(function ($district) {
                $district['type'] = 'district';
                return $district;
            });
        }

        return response()->json($address);
    }
}
