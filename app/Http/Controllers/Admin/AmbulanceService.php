<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Service;
use App\Models\ServiceFeature;
use App\Models\ServiceImage;
use App\Models\ApplyService;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// For CSV File Download
use League\Csv\Writer;
use SplTempFileObject;

class AmbulanceService extends Controller
{
    public function show_update_page(Request $request)
    {
        $categories = ServiceCategory::where('category_type', 'ambulance')->first();

        $service = Service::whereHas('category', function ($q) {
            $q->where('category_type', 'ambulance');
        })->first();

        if (is_null($categories)) {
            $ambulance = new ServiceCategory();
            $ambulance->name = "ambulance service";
            $ambulance->logo = "public/assets/logo/ambulance-logo.jpg";
            $ambulance->category_type = "ambulance";
            $ambulance->save();

            if(is_null($service)){
                $ser = new Service();
                $ser->name = "Ambulance service title"; 
                $ser->short_description = "Ambulance service short description"; 
                $ser->description = "Ambulance service description";
                $ser->category_id = $ambulance->id;
                $ser->save();
            };
            Log::info('service.ambulance');
        };

        return view('admin-views.ambulance.edit-ambulance', compact('service', 'categories'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            's_description' => 'nullable|string',
            'description' => 'nullable|string',
            'category_id' => 'required',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Find the service by ID
        $service = Service::findOrFail($id);
        $category = DB::table('service_categories')->where('id', $validatedData['category_id'])->first();

        // Update service data
        $service->name = $validatedData['name'];
        $service->short_description = $validatedData['s_description'];
        $service->description = $validatedData['description'];
        $service->save();

        // Handle images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Save the image to storage
                $path = $image->store('service_images', 'public');

                // Create a new ServiceImage record
                $serviceImage = new ServiceImage();
                $serviceImage->service_id = $service->id;
                $serviceImage->image_path = $path;
                $serviceImage->save();
            }
        }

        Toastr::success("Ambulance Information updated successfully!", "Success");
        return redirect()->back();
    }
}
