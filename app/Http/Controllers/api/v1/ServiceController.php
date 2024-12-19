<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\service_banner;
use App\Models\ApplyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ServiceCategory;
use App\Services\SmsModule;


class ServiceController extends Controller
{
    // Service/Car/Property List API
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $categoryName = $request->input('category_name');
        $categoryType = $request->input('category_type');

        $query = Service::with(['category', 'images', 'features']);

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhereHas('features', function ($query) use ($search) {
                        $query->where('feature', 'like', "%$search%");
                    });
            });
        }

        // Filter by category ID
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Filter by category name
        if ($categoryName) {
            $query->whereHas('category', function ($query) use ($categoryName) {
                $query->where('name', 'like', "%$categoryName%");
            });
        }

        // Filter by category type
        if ($categoryType) {
            $query->whereHas('category', function ($query) use ($categoryType) {
                $query->where('category_type', $categoryType);
            });
        }

        $services = $query->paginate($perPage);

        // Transform the services to include category details with only necessary fields
        $services->getCollection()->transform(function ($service) {
            $category = $service->category;
            $service->category = [
                'id' => $category->id,
                'name' => $category->name,
                'logo' => $category->logo
            ];
            return $service;
        });

        $response = [
            'total_size' => $services->total(),
            'limit' => $services->perPage(),
            'data' => [
                'current_page' => $services->currentPage(),
                'services' => $services->items(),
            ],
            'first_page_url' => $services->url(1),
            'from' => $services->firstItem(),
            'last_page' => $services->lastPage(),
            'last_page_url' => $services->url($services->lastPage()),
            'next_page_url' => $services->nextPageUrl(),
            'prev_page_url' => $services->previousPageUrl(),

            // 'links' => $services->links(),
            // 'path' => $services->path(),
            // 'per_page' => $services->perPage(),
            // 'to' => $services->lastItem(),
            // 'total' => $services->total(),
        ];

        return response()->json($response, 200);
    }


    // Details API
    // public function show($id)
    // {
    //     $service = Service::with('images', 'features')->findOrFail($id);
    //     return response()->json(['data' => $service], 200);
    // }

    public function show($id)
    {
        $service = Service::with(['category', 'images', 'features'])->findOrFail($id);

        // Structure the response data to include necessary fields
        $serviceDetails = [
            'id' => $service->id,
            'name' => $service->name,
            'short_description' => $service->short_description,
            'description' => $service->description,
            'price' => $service->price,
            'category' => [
                'id' => $service->category->id,
                'name' => $service->category->name,
                'logo' => $service->category->logo,
            ],
            'images' => $service->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'service_id' => $image->service_id,
                    'image_path' => $image->image_path,
                ];
            }),
            'features' => $service->features->map(function ($feature) {
                return [
                    'id' => $feature->id,
                    'service_id' => $feature->service_id,
                    'feature' => $feature->feature,
                ];
            }),
        ];

        return response()->json(['data' => $serviceDetails], 200);
    }



    // Apply Service API
    public function apply(Request $request)
    {
        // \Log::info('Request data:', $request->all());

        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized user'], 401);
        }

        $user = Auth::user();

        // if (!$user->is_membership) {
        //     return response()->json(['error' => 'Please apply for membership first, then you can apply for service'], 422);
        // }

        // $membership = $user->membership;
        // if ($membership->status === 'pending') {
        //     return response()->json(['error' => 'Your membership request is pending, please patiently wait'], 422);
        // } elseif ($membership->status === 'rejected') {
        //     return response()->json(['error' => 'Your membership request is rejected, please contact with us.'], 422);
        // }


        $validatedData = $request->validate([
            'service_id' => 'required',
            'phone' => 'required',
            'description' => 'required',
        ]);

        $service = Service::where('id', $validatedData['service_id'])->first();
        if (!$service) {
            return response()->json(['error' => 'Service not found.'], 422);
        }

        // Check if the user has applied for the same service recently
        $lastApplication = ApplyService::where('user_id', $user->id)
                ->where('service_id', $service->id)
                ->orderByDesc('created_at')
                ->first();

        // Calculate the difference in days
        if ($lastApplication) {
            $daysDifference = now()->diffInDays($lastApplication->created_at);

            if ($daysDifference < 15) {
                return response()->json(['error' => 'You cannot apply for the same service again within 1 days.'], 422);
            }
        }

        // Create a new ApplyService record
        $applyService = new ApplyService();
        $applyService->user_id = $user->id;
        $applyService->service_id = $service->id;
        $applyService->phone = $validatedData['phone'];
        $applyService->description = $validatedData['description'];

        $applyService->save();

        // Send SMS after order placement
        $user = $request->user();

        if ($user->phone) {
            $msg = "আপনার সর্ভস রিকুয়েস্ট টি গ্রহন করা হয়েছে, \n ধন্যবাদ, \n আমাদের সাথে থাকার জন্যে \n Shojonsl.com";

            $res = SmsModule::sendSms_greenweb($user, $msg);
            Log::info('SMS Response = ' . $res);
        }

        return response()->json(['message' => 'Service applied successfully'], 200);
    }



    public function category_list(Request $request)
    {
        $categoryType = $request->input('category_type', null);
        $query = ServiceCategory::query();

        // $serviceCategories = ServiceCategory::all();

        if ($categoryType) {
            $query->where('category_type', $categoryType);
        }

        $serviceCategories = $query->get();

        $categoriesData = $serviceCategories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'category_type' => $category->category_type,
                'logo' => $category->logo ?? null,
            ];
        });

        $response = [
            'total_size' => $categoriesData->count(),
            'data' => [
                'categories' => $categoriesData,
            ],
        ];

        return response()->json($response, 200);
    }

    public function banner_list(Request $request)
    {
        $banner_type = $request->input('banner_type', null);
        $query = service_banner::query();

        // $serviceCategories = ServiceCategory::all();

        if ($banner_type) {
            $query->where('banner_type', $banner_type);
        }

        $serviceBanner = $query->get();

        $bannerData = $serviceBanner->map(function ($banner) {
            return [
                'id' => $banner->id,
                'banner_type' => $banner->banner_type,
                'title' => $banner->title,
                'descriptions' => $banner->descriptions,
                'banner_image' => $banner->banner_image ?? null,
            ];
        });

        $response = [
            'total_size' => $bannerData->count(),
            'data' => [
                'banner' => $bannerData,
            ],
        ];

        return response()->json($response, 200);
    }


    public function applyServicesList(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized user'], 401);
        }

        $user = Auth::user();

        // Fetch applied services for the user with pagination
        $perPage = $request->query('limit', 10); // Default to 10 items per page
        $appliedServices = ApplyService::where('user_id', $user->id)
            ->with(['service' => function ($query) {
                $query->with('category', 'images', 'features');
            }])
            ->paginate($perPage);

        // Manipulate the response to modify the service structure
        $modifiedServices = $appliedServices->map(function ($applyService) {
            return [
                'id' => $applyService->id,
                'created_at' => $applyService->created_at,
                'updated_at' => $applyService->updated_at,
                'user_id' => $applyService->user_id,
                'service_id' => $applyService->service_id,
                'phone' => $applyService->phone,
                'description' => $applyService->description,
                'service' => [
                    'id' => $applyService->service->id,
                    'name' => $applyService->service->name,
                    'price' => $applyService->service->price,
                    'category_id' => $applyService->service->category_id,
                    'category' => [
                        'id' => $applyService->service->category->id,
                        'name' => $applyService->service->category->name,
                        'logo' => $applyService->service->category->logo,
                    ],
                    'images' => $applyService->service->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'service_id' => $image->service_id,
                            'image_path' => $image->image_path,
                        ];
                    }),
                    'features' => $applyService->service->features->map(function ($feature) {
                        return [
                            'id' => $feature->id,
                            'service_id' => $feature->service_id,
                            'feature' => $feature->feature,
                        ];
                    }),
                ],
            ];
        });


        $response = [
            'total_size' => $appliedServices->total(),
            'limit' => $appliedServices->perPage(),
            'data' => [
                'current_page' => $appliedServices->currentPage(),
                'ApplyServices' => $modifiedServices,
            ],
            'from' => $appliedServices->firstItem(),
            'last_page' => $appliedServices->lastPage(),
            'last_page_url' => $appliedServices->url($appliedServices->lastPage()),
            'next_page_url' => $appliedServices->nextPageUrl(),
            'prev_page_url' => $appliedServices->previousPageUrl(),
        ];

        return response()->json($response, 200);
    }



    public function applyServiceDetail($id)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized user'], 401);
        }

        $user = Auth::user();

        // Find the ApplyService by ID and make sure it belongs to the authenticated user
        $applyService = ApplyService::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['service' => function ($query) {
                $query->with('category', 'images', 'features');
            }])
            ->first();

        if (!$applyService) {
            return response()->json(['error' => 'ApplyService not found'], 404);
        }

        // Structure the response data
        $serviceDetails = [
            'id' => $applyService->id,
            'created_at' => $applyService->created_at,
            'updated_at' => $applyService->updated_at,
            'user_id' => $applyService->user_id,
            'service_id' => $applyService->service_id,
            'phone' => $applyService->phone,
            'description' => $applyService->description,
            'service' => [
                'id' => $applyService->service->id,
                'name' => $applyService->service->name,
                'price' => $applyService->service->price,
                'category_id' => $applyService->service->category_id,
                'category' => [
                    'id' => $applyService->service->category->id,
                    'name' => $applyService->service->category->name,
                    'logo' => $applyService->service->category->logo,
                ],

                'thumbnail' => $applyService->service->images->first(),

                'images' => $applyService->service->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'service_id' => $image->service_id,
                        'image_path' => $image->image_path,
                    ];
                }),
                'features' => $applyService->service->features->map(function ($feature) {
                    return [
                        'id' => $feature->id,
                        'service_id' => $feature->service_id,
                        'feature' => $feature->feature,
                    ];
                }),
            ],
        ];

        $response = [
            'title'=> "Apply service details",
            'data' => $serviceDetails
        ];

        return response()->json($response, 200);
    }






}

