<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ApplyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ServiceCategory;

class AmbulanceController extends Controller
{
    public function show(Request $request)
    {
        // $categories = ServiceCategory::where('category_type', 'ambulance')->first();

        $service = Service::whereHas('category', function ($q) {
            $q->where('category_type', 'ambulance');
        })->first();

        // Structure the response data to include necessary fields
        $serviceDetails = [
            'service_id' => $service->id,
            'name' => $service->name,
            'short_description' => $service->short_description,
            'description' => $service->description,
            // 'category' => [
            //     'id' => $service->category->id,
            //     'name' => $service->category->name,
            //     'logo' => $service->category->logo,
            // ],

            'thumbnail_image' => $service->images->first() ? $service->images->first()->image_path : null,
            'images' => $service->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'service_id' => $image->service_id,
                    'image_path' => $image->image_path,
                ];
            }),
        ];

        return response()->json(['data' => $serviceDetails], 200);
    }
}
