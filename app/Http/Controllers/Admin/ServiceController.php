<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Service;
use App\Models\ServiceFeature;
use App\Models\ServiceImage;
use App\Models\ApplyService;
use App\Models\ServiceCategory;
use App\Models\service_banner;
use Illuminate\Support\Facades\DB;

// For CSV File Download
use League\Csv\Writer;
use SplTempFileObject;


class ServiceController extends Controller
{
    public function add_new_service()
    {
        // $categories = ServiceCategory::all();
        $categories = ServiceCategory::where('category_type', 'service')->get();
        return view('admin-views.service.add-new-servic', compact('categories'));
    }


    public function show_update_page(Request $request, $id)
    {
        // $categories = ServiceCategory::all();
        $categories = ServiceCategory::where('category_type', 'service')->get();
        $service = Service::findOrFail($id);

        return view('admin-views.service.edit-service', compact('service', 'categories'));
    }


    public function store(Request $request)
    {

        // \Log::info('Request data:', $request->all());

        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'purchase_price' => 'nullable|numeric',

            'category_id' => 'required',

            'feature_name.*' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $category = DB::table('service_categories')->where('id', $validatedData['category_id'])->first();

        // \Log::info('Category =:', $category);

        // Create the service
        $service = new Service();
        $service->name = $validatedData['name'];
        $service->description = $validatedData['description'];
        $service->price = $validatedData['purchase_price'];
        $service->category_id = $category->id;
        $service->save();

        // Save features
        if (isset($validatedData['feature_name'])) {
            foreach ($validatedData['feature_name'] as $feature) {
                $serviceFeature = new ServiceFeature();
                $serviceFeature->service_id = $service->id;
                $serviceFeature->feature = $feature;
                $serviceFeature->save();
            }
        }

        // Save images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // $path = $image->store('service_images');
                $path = $image->store('service_images', 'public');

                $serviceImage = new ServiceImage();
                $serviceImage->service_id = $service->id;
                $serviceImage->image_path = $path;
                $serviceImage->save();
            }
        }

        // Flash success message to session
        // session()->flash('success', 'New Service Created Successfully');

        Toastr::success("New Service Created Successfully!", "Success");
        return redirect()->route('admin.service.list');
    }


    public function list(Request $request)
    {
        $query_param = [];
        $search = $request->input('search');

        // $services = Service::query();
        $services = Service::query()
            ->whereHas('category', function ($query) {
                $query->where('category_type', 'service');
            });

        if ($request->has('search')) {
            // Search by service name
            $services = $services->where('name', 'like', "%{$search}%");

            // Search by category name
            $categoryIds = ServiceCategory::where('name', 'like', "%{$search}%")->pluck('id');
            $services = $services->orWhereIn('category_id', $categoryIds);

            $query_param = ['search' => $search];
        }

        // Check if the request is for CSV download
        if ($request->has('download') && $request->input('download') === 'csv') {
            $services = $services->get(); // Get all records for CSV
            return $this->downloadCsv($services);
        }

        $services = $services->orderBy('id', 'DESC')->paginate()->appends($query_param);
        return view('admin-views.service.service-list', compact('services', 'search'));
    }


    private function downloadCsv($services)
    {
        $csv = Writer::createFromFileObject(new SplTempFileObject());

        // Insert header
        $csv->insertOne([
            'Service ID',
            'Service Name',
            'Service Category',
            'Service Features',
            'Created At',
            'Updated At'
        ]);

        // Insert rows
        foreach ($services as $service) {
            // Retrieve features as a comma-separated string
            $features = $service->features->pluck('feature')->implode(', ');

            // Retrieve category name
            $categoryName = $service->category ? $service->category->name : 'N/A';

            $csv->insertOne([
                $service->id,
                $service->name,
                $categoryName,
                $features,
                $service->created_at->format('Y-m-d H:i:s'),
                $service->updated_at->format('Y-m-d H:i:s'),
            ]);
        }

        // Return CSV file as response
        $csv->output('services.csv');
        exit;
    }



    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'purchase_price' => 'nullable|numeric',

            'category_id' => 'required',

            'feature_name.*' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Find the service by ID
        $service = Service::findOrFail($id);
        $category = DB::table('service_categories')->where('id', $validatedData['category_id'])->first();

        // Update service data
        $service->name = $validatedData['name'];
        $service->description = $validatedData['description'];
        $service->price = $validatedData['purchase_price'];
        $service->category_id = $category->id;
        $service->save();

        // Handle features
        if (isset($validatedData['feature_name'])) {
            // Delete existing features associated with the service
            $service->features()->delete();

            // Insert the new features
            foreach ($validatedData['feature_name'] as $feature) {
                $serviceFeature = new ServiceFeature();
                $serviceFeature->service_id = $service->id;
                $serviceFeature->feature = $feature;
                $serviceFeature->save();
            }
        }

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

        Toastr::success("Service updated successfully!", "Success");
        return redirect()->back();
    }




    public function delete($id)
    {

        $service = Service::findOrFail($id);

        // Delete associated features
        $service->features()->delete();

        // Delete associated images from storage
        foreach ($service->images as $image) {
            Storage::delete($image->image_path);
        }

        // Delete the images records
        $service->images()->delete();

        // Delete the service
        $service->delete();

        Toastr::success("Service and its related features and images deleted successfully!", "Success");
        return redirect()->back();
    }

    public function feature_delete(Request $request, $id)
    {
        // \Log::info('Deleted Feature id is : ' . $id);

        $serviceFeature = ServiceFeature::findOrFail($id);
        $serviceFeature->delete();

        return response()->json(['success' => true]);
    }


    public function image_delete(Request $request, $id)
    {

        $serviceImage = ServiceImage::findOrFail($id);

        // Delete the image file from storage
        Storage::delete('public/storage' . $serviceImage->image_path);
        // store('service_images', 'public');

        // Delete the image record from the database
        $serviceImage->delete();

        return response()->json(['success' => true]);
    }


    // Apply Service List
    public function apply_service_list(Request $request)
    {
        $query_param = [];
        $search = $request->input('search');

        $applyServices = ApplyService::query();

        // Search by user name, email, and phone
        if ($request->filled('search')) {
            $applyServices->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
            $query_param['search'] = $search;
        }

        // Search by service name
        if ($request->filled('search')) {
            $applyServices->orWhereHas('service', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
            $query_param['search'] = $search;
        }

        // Filter by apply date
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $applyServices->whereBetween('created_at', [$fromDate, $toDate]);
            $query_param['from_date'] = $fromDate;
            $query_param['to_date'] = $toDate;
        }

        // Filter by category type
        if ($request->filled('category_type') && $request->input('category_type') !== 'all') {
            $categoryType = $request->input('category_type');
            $applyServices->whereHas('service.category', function ($query) use ($categoryType) {
                $query->where('category_type', $categoryType);
            });
            $query_param['category_type'] = $categoryType;
        }

        // Order by created_at in descending order
        $applyServices->orderBy('created_at', 'DESC');

        // Check if the request is for CSV download
        if ($request->has('download') && $request->input('download') === 'csv') {
            $applyServices = $applyServices->get(); // Get all records for CSV
            return $this->apply_service_downloadCsv($applyServices);
        }

        $services = $applyServices->paginate()->appends($query_param);
        return view('admin-views.service.apply_service_list', compact('services', 'search'));
    }


    private function apply_service_downloadCsv($applyServices)
    {
        $csv = Writer::createFromFileObject(new SplTempFileObject());

        // Insert header
        $csv->insertOne([
            'Apply Service ID',

            'Customer ID',
            'Customer Name',
            'Customer Email',
            'Customer Phone Number',

            'Service ID',
            'Service Name',
            'Service Category Type',
            'Service Category',

            'Contact Number',
            'Description',

            'Apply Date',
            // 'Updated At'
        ]);

        // Insert rows
        foreach ($applyServices as $applyService) {

            $csv->insertOne([
                $applyService->id,

                $applyService->user_id,
                $applyService->user->fullname,
                $applyService->user->email,
                $applyService->user->phone,

                $applyService->service_id,
                $applyService->service->name,
                $applyService->service->category->category_type,
                $applyService->service->category->name,

                $applyService->phone,
                $applyService->description,

                $applyService->created_at->format('Y-m-d H:i:s'),
                // $applyService->updated_at->format('Y-m-d H:i:s'),
            ]);
        }

        // Return CSV file as response
        $csv->output('apply_services.csv');
        exit;
    }



    public function apply_service_details(string $id)
    {
        $apply_service = ApplyService::findOrFail($id);

        // $customer->load('membership'); // Load the user's membership if it exists

        return view('admin-views.service.apply_service_details', compact('apply_service'));
    }



    public function apply_service_delete($id)
    {

        $service = ApplyService::findOrFail($id);
        $service->delete();
        Toastr::success("Apply service delete successfully!", "Success");

        return redirect()->back();
    }




    public function category_list(Request $request)
    {
        $query_param = [];
        $search = $request->input('search');

        $cat = ServiceCategory::query();

        // Exclude category_type = 'ambulance'
        $cat->where('category_type', '!=', 'ambulance');

        // Check if there is a search query
        if ($search) {
            $key = explode(' ', $search);

            $cat->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->where('name', 'like', "%{$value}%");
                }
            });

            $query_param = ['search' => $search];
        }

        $categories = $cat->orderBy('id', 'DESC')->paginate()->appends($query_param);
        return view('admin-views.service.category.list', compact('categories', 'search'));
    }



    public function category_create_index()
    {
        return view('admin-views.service.category.create');
    }

    public function category_create_store(Request $request)
    {
        // dd($request->all());

        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category_type' => 'required|in:service,car,property',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Store the logo file
        $logoPath = $request->file('logo')->store('ServiceCategory', 'public');

        // Create a new ServiceCategory instance and save it
        $category = new ServiceCategory();
        $category->name = $validatedData['name'];
        $category->category_type = $validatedData['category_type'];
        $category->logo = $logoPath;
        $category->save();

        // Display a success message and redirect back
        Toastr::success("New Service Category Created Successfully!", "Success");
        return redirect()->back();
    }


    public function category_update_index(Request $request, $id)
    {
        $category = ServiceCategory::findOrFail($id);
        return view('admin-views.service.category.update', compact('category'));
    }

    public function category_update_store(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $category = ServiceCategory::findOrFail($id);

        $category->name = $validatedData['name'];

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('ServiceCategory', 'public');
            $category->logo = $logoPath;
        }

        $category->save();

        Toastr::success("Service Category Updated Successfully!", "Success");
        return redirect()->back();
    }


    public function category_delete($id)
    {

        $category = ServiceCategory::findOrFail($id);

        if ($category->logo) {
            Storage::disk('public')->delete($category->logo);
        }

        $category->delete();

        Toastr::success("Service Category Deleted Successfully!", "Success");
        return redirect()->back();
    }



    // Banner Service
    function banner_list(Request $request)
    {
        $service_ba = service_banner::where('banner_type', 'service')->first();
        if (is_null($service_ba)) {
            $banner = new service_banner();
            $banner->title = "Service Banner";
            $banner->banner_type = "service";
            $banner->save();
        };

        $car_ba = service_banner::where('banner_type', 'car')->first();
        if (is_null($car_ba)) {
            $banner = new service_banner();
            $banner->title = "Car Banner";
            $banner->banner_type = "car";
            $banner->save();
        };

        $property_ba = service_banner::where('banner_type', 'property')->first();
        if (is_null($property_ba)) {
            $banner = new service_banner();
            $banner->title = "Property Banner";
            $banner->banner_type = "property";
            $banner->save();
        };

        $ambulance_ba = service_banner::where('banner_type', 'ambulance')->first();
        if (is_null($ambulance_ba)) {
            $banner = new service_banner();
            $banner->title = "Abmulance Banner";
            $banner->banner_type = "ambulance";
            $banner->save();
        };

        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $br = service_banner::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('title', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $br = new service_banner();
        }

        $banners = $br->orderBy('id', 'DESC')->paginate()->appends($query_param);
        return view('admin-views.service.banner.list', compact('banners', 'search'));
    }

    public function banner_edit(Request $request, $id)
    {
        $banner = service_banner::findOrFail($id);
        return view('admin-views.service.banner.edit', compact('banner'));
    }


    public function banner_update(Request $request, $id)
    {

        $validatedData = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'banner_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $banner = service_banner::findOrFail($id);

        $banner->title = $validatedData['title'];
        $banner->descriptions = $validatedData['description'];

        if ($request->hasFile('banner_image')) {
            $logoPath = $request->file('banner_image')->store('ServiceBanner', 'public');
            $banner->banner_image = $logoPath;
        }

        $banner->save();

        Toastr::success("Banner Updated Successfully!", "Success");
        return redirect()->back();
    }

}
