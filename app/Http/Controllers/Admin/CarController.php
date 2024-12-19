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
use Illuminate\Support\Facades\DB;

// For CSV File Download
use League\Csv\Writer;
use SplTempFileObject;

class CarController extends Controller
{
    public function add_new_car()
    {
        // $categories = ServiceCategory::all();
        $categories = ServiceCategory::where('category_type', 'car')->get();
        return view('admin-views.car.add-new-car', compact('categories'));
    }


    public function show_update_page(Request $request, $id)
    {
        // $categories = ServiceCategory::all();
        $categories = ServiceCategory::where('category_type', 'car')->get();
        $service = Service::findOrFail($id);

        return view('admin-views.car.edit-car', compact('service', 'categories'));
    }


    public function store(Request $request)
    {

        // \Log::info('Request data:', $request->all());

        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            's_description' => 'nullable|string',
            'purchase_price' => 'nullable|numeric',

            'category_id' => 'required',

            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $category = DB::table('service_categories')->where('id', $validatedData['category_id'])->first();

        // \Log::info('Category =:', $category);

        // Create the service
        $service = new Service();
        $service->name = $validatedData['name'];
        $service->short_description = $validatedData['s_description'];
        $service->description = $validatedData['description'];
        $service->price = $validatedData['purchase_price'];
        $service->category_id = $category->id;
        $service->save();

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

        Toastr::success("New Car Service Created Successfully!", "Success");
        return redirect()->route('admin.car.list');
    }


    public function list(Request $request)
    {
        $query_param = [];
        $search = $request->input('search');

        // $services = Service::query();
        $services = Service::query()
            ->whereHas('category', function ($query) {
                $query->where('category_type', 'car');
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
        return view('admin-views.car.car-list', compact('services', 'search'));
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

        Toastr::success("Car Service and its related images deleted successfully!", "Success");
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            's_description' => 'nullable|string',
            'description' => 'nullable|string',
            'purchase_price' => 'nullable|numeric',

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
        $service->price = $validatedData['purchase_price'];
        $service->category_id = $category->id;
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

        Toastr::success("Car Service updated successfully!", "Success");
        return redirect()->back();
    }

    private function downloadCsv($services)
    {
        $csv = Writer::createFromFileObject(new SplTempFileObject());

        // Insert header
        $csv->insertOne([
            'Service ID',
            'Service Name',
            'Service Category',
            'Short Description',
            // 'Description',
            'Created At',
            'Updated At'
        ]);

        // Insert rows
        foreach ($services as $service) {
            // Retrieve category name
            $categoryName = $service->category ? $service->category->name : 'N/A';

            $csv->insertOne([
                $service->id,
                $service->name,
                $categoryName,
                $service->short_description,
                // $service->description,
                $service->created_at->format('Y-m-d H:i:s'),
                $service->updated_at->format('Y-m-d H:i:s'),
            ]);
        }

        // Return CSV file as response
        $csv->output('services.csv');
        exit;
    }
}
