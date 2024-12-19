<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Membership;
use App\Models\Coupon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PointHistory;
use Illuminate\Support\Facades\Auth;

class MembershipController extends Controller
{
    public function store(Request $request)
    {
 
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

       
        $existingMembership = Membership::where('user_id', $user->id)->first();
        if ($existingMembership && $existingMembership->status !== 'approved') {

            if($existingMembership->status == 'rejected'){
                return response()->json(['error' => 'Your membership request is rejected. Please contact with us.'], 422);
            }
            else{
                return response()->json(['error' => 'Your membership request is under review. Please wait patiently.'], 422);
            }
        }

     
        $existingMembership = Membership::where('user_id', $user->id)->first();
        if ($existingMembership) {
            return response()->json(['error' => 'User already has a membership.'], 422);
        }

   
        $validatedData = $request->validate([
            'verification_type' => 'required|in:nid,passport',
            'verification_id' => 'required|string|max:20|unique:memberships,verification_id',
        ]);

        // Generate a unique referral_id
        $referralId = strtoupper(bin2hex(random_bytes(5)));


        $referredMemberId = null;

        // Check if referred_from is provided in the request
        if ($request->has('referred_from')) {
            // Find the membership based on the referral_id
            $referredFrom = Membership::where('referral_id', $request->input('referred_from'))->first();

            // If the membership is not found, return an error response
            if (!$referredFrom) {
                return response()->json(['error' => 'Your Referral ID is incorrect'], 422);
            }

            // If the membership is found, set the referred_member_id
            $referredMemberId = $referredFrom->user_id;
        }

        // Create a new membership
        $membershipData = [
            'user_id' => $user->id,
            'referred_from' => $referredMemberId,
            'referral_id' => $referralId,
            'points' => 0,
            'status' => 'pending',
            'verification_type' => $validatedData['verification_type'],
            'verification_id' => $validatedData['verification_id'],
        ];


        // Create the membership
        $membership = Membership::create($membershipData);

        // Return a response with 201 status code
        return response()->json(['message' => 'Membership created successfully', 'membership' => $membership], 201);
    }



    public function retrieve(Request $request)
    {
        // Retrieve the authenticated user from the request token
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        // Retrieve the membership data for the authenticated user
        $membership = $user->membership;

        if (!$membership) {
            return response()->json(['error' => 'User does not have a membership.'], 404);
        }

        // Check if the membership status is 'approved'
        if ($membership->status !== 'approved') {
            return response()->json(['error' => 'Your membership request is not yet approved.'], 422);
        }

        // Determine point conversion info
        $lastCoupon = Coupon::where('user_id', $user->id)
            ->where('coupon_type', 'points_conversion')
            ->orderBy('created_at', 'desc')
            ->first();

        $pointInfo = 'You can convert points now.';
        if ($lastCoupon) {
            $nextAllowedDate = Carbon::parse($lastCoupon->created_at)->addDays(30);
            $now = Carbon::now();
            if ($now->lt($nextAllowedDate)) {
                $daysRemaining = $now->diffInDays($nextAllowedDate);
                $pointInfo = "You can convert points $daysRemaining days later.";
            }
        }

        // Return the membership data and point_info as a JSON response
        return response()->json([
            'membership' => $membership,
            'point_info' => $pointInfo
        ]);
    }



    public function points_conversion(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        // Log::info('User =:'. $user);

        $existingMembership = Membership::where('user_id', $user->id)->first();

        if ($existingMembership && $existingMembership->status !== 'approved') {
            if ($existingMembership->status == 'rejected') {
                return response()->json(['error' => 'Your membership request is rejected. Please contact us.'], 422);
            } else {
                return response()->json(['error' => 'Your membership request is under review. Please wait patiently.'], 422);
            }
        }

        // Log::info('Membership =:'. $existingMembership);

        $validatedData = $request->validate([
            'points' => ['required', 'numeric', 'min:100', 'max:2000'],
        ]);

        // Log::info('Points =:'. $validatedData['points']);

        $requestedPoints = $request->points;
        if ($existingMembership->points < $requestedPoints) {
            return response()->json(['error' => 'Insufficient points.'], 422);
        }

        $lastCoupon = Coupon::where('user_id', $user->id)
            ->where('coupon_type', 'points_conversion')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastCoupon) {
            $nextAllowedDate = Carbon::parse($lastCoupon->created_at)->addDays(30);
            $now = Carbon::now();
            if ($now->lt($nextAllowedDate)) {
                $daysRemaining = $now->diffInDays($nextAllowedDate);
                return response()->json(['error' => "You can convert points $daysRemaining days later."], 422);
            }
        }

        $coupon = null;

        if ($requestedPoints >= 100 && $requestedPoints <= 2000) {
            $couponCode = strtoupper(bin2hex(random_bytes(3)));
            $expireDate = Carbon::now()->addDays(30);

            $coupon = Coupon::create([
                'coupon_type' => 'points_conversion',
                'code' => $couponCode,
                'title' => 'points coupon',
                'created_at' => now(),
                'updated_at' => now(),
                'start_date' => now(),
                'expire_date' => $expireDate,
                'limit' => 1,
                'status' => 1,
                'user_id' => $user->id,
                'discount_type' => 'amount',
                'discount' => $requestedPoints,
            ]);

            $existingMembership->points -= $requestedPoints;
            $existingMembership->save();
        } else {
            return response()->json(['error' => 'Invalid points range. Points should be between 100 and 2000.'], 422);
        }

        return response()->json([
            'message' => 'Successfully converted your points.',
            'coupon' => $coupon
        ], 201);
    }



    public function CustomerCoupons(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $membership = $user->membership;

        if (!$membership) {
            return response()->json(['error' => 'User does not have a membership.'], 404);
        }

        if ($membership->status !== 'approved') {
            return response()->json(['error' => 'Your membership request is not yet approved.'], 422);
        }

        $coupons = Coupon::where('user_id', $user->id)
            ->where('status', '>=', 1)
            ->where('limit', '>=', 1)
            ->whereDate('expire_date', '>=', now())
            ->get();



            $response_data = $coupons->map(function ($coupon) {
                return [
                    'id' => $coupon->id,
                    // 'created_at' => $coupon->created_at,
                    // 'updated_at' => $coupon->updated_at,
                    'user_id' => $coupon->user_id,
                    'title' => $coupon->title,
                    'code' => $coupon->code,
                    'start_date' => $coupon->start_date,
                    'expire_date' => $coupon->expire_date,
                    'discount' => $coupon->discount,
                    'discount_type' => $coupon->discount_type,
                    'status' => $coupon->status,
                    'limit' => $coupon->limit,
  
                ];
            });

        $response = [
            'total_size' => $coupons->count(),
            'data' => [
                'title' => "Customer Coupons List", 
                'Coupons' => $response_data,
            ],
        ];

        return response()->json($response, 200);
    }

    public function all_referred(Request $request)
    {
        $user = Auth::user();

        // Get all referred users
        $referredUsers = $user->referrals()->with('user:id,f_name,l_name,email,phone')->get();

        // Format the response
        $data = $referredUsers->map(function ($referral) {
            return [
                'id' => $referral->user->id,
                'name' => $referral->user->f_name . ' ' . $referral->user->l_name,
                'email' => $referral->user->email,
                'phone' => $referral->user->phone,
            ];
        });

        $response = [
            'total_size' => $referredUsers->count(),
            'data' => $data
        ];

        return response()->json($response, 200);
    }





    public function point_history(Request $request)
    {
        $request->validate([
            'limit' => 'integer|min:1',
            'referred_id' => 'integer|nullable',
            'status' => 'in:self,referred|nullable', 
        ]);

        $user = Auth::user();

        $perPage = $request->input('limit', 10);
        $referredId = $request->input('referred_id');
        $status = $request->input('status');

        $pointHistoryQuery = PointHistory::with(['referredUser:id,f_name,l_name,email,phone'])
            ->where('user_id', $user->id);

        if ($referredId) {
            // Log::info("referred id =" . (int) $referredId);
            $pointHistoryQuery->where('referred_user', (int) $referredId);
        }

        if ($status) {
            $pointHistoryQuery->where('status', $status);
        }

        $ptHistory = $pointHistoryQuery->paginate($perPage);

        $totalPoints = $pointHistoryQuery->sum('points');

        // Prepare the response
        $response = [
            'total_size' => $ptHistory->total(),
            'limit' => $ptHistory->perPage(),
            'referredId' => $referredId,
            'status' => $status,
            'data' => [
                    'current_page' => $ptHistory->currentPage(),
                    'total_point' => number_format($totalPoints, 2),
                    'histories' => $ptHistory->items(),
                ],
            'first_page_url' => $ptHistory->url(1),
            'from' => $ptHistory->firstItem(),
            'last_page' => $ptHistory->lastPage(),
            'last_page_url' => $ptHistory->url($ptHistory->lastPage()),
            'next_page_url' => $ptHistory->nextPageUrl(),
            'prev_page_url' => $ptHistory->previousPageUrl(),
        ];

        return response()->json($response, 200);
    }





}








