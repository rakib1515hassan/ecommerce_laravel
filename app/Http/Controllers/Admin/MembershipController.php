<?php

namespace App\Http\Controllers\Admin;

use App\Services\AdditionalServices;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Membership;
use App\Models\PointHistory;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use App\Services\SmsModule;

// For CSV Document Download
use League\Csv\Writer;
use SplTempFileObject;


class MembershipController extends Controller
{
    // public function index(Request $request)
    // {
    //     $query_param = [];
    //     $search = $request->input('search');
    //     $from_date = $request->input('from_date');
    //     $to_date   = $request->input('to_date');

    //     $customers = User::with(['membership'])->whereHas('membership');

    //     if (!empty($from_date) && !empty($to_date)) {
    //         $customers->whereHas('membership', function ($query) use ($from_date, $to_date) {
    //             $query->whereBetween('created_at', [$from_date, $to_date]);
    //         });
    //     }

    //     if (!empty($search)) {
    //         $key = explode(' ', $search);
    //         $customers->where(function ($query) use ($key) {
    //             foreach ($key as $value) {
    //                 $query->orWhere('f_name', 'like', "%{$value}%")
    //                     ->orWhere('l_name', 'like', "%{$value}%")
    //                     ->orWhere('phone', 'like', "%{$value}%")
    //                     ->orWhere('email', 'like', "%{$value}%")
    //                     ->orWhereHas('membership', function ($query) use ($value) {
    //                         $query->where('referral_id', 'like', "%{$value}%");
    //                     });
    //             }
    //         });
    //         $query_param['search'] = $search;
    //     }

    //     $customers = $customers->latest()  //ASC(latest())  or DESC (oldest())
    //                             ->paginate(AdditionalServices::pagination_limit())
    //                             ->appends($query_param);


    //     foreach ($customers as $customer) {
    //         $referralCount = $customer->referrals()->count();
    //         $customer->referral_count = $referralCount;
    //     }

    //     return view('admin-views.membership.list', compact('customers', 'search'));
    // }





    public function index(Request $request)
    {
        $query_param = [];
        $search = $request->input('search');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $customersQuery = User::with(['membership'])->whereHas('membership');

        if (!empty($from_date) && !empty($to_date)) {
            $customersQuery->whereHas('membership', function ($query) use ($from_date, $to_date) {
                $query->whereBetween('created_at', [$from_date, $to_date]);
            });
        }

        if (!empty($search)) {
            $key = explode(' ', $search);
            $customersQuery->where(function ($query) use ($key) {
                foreach ($key as $value) {
                    $query->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->orWhereHas('membership', function ($query) use ($value) {
                            $query->where('referral_id', 'like', "%{$value}%");
                        });
                }
            });
            $query_param['search'] = $search;
        }

        // Check if the request is for CSV download
        if ($request->has('download') && $request->download === 'csv') {
            $customers = $customersQuery->latest()->get();
            return $this->downloadCsv($customers);
        }

        // Paginate for displaying in the view
        $customers = $customersQuery->latest()
            ->paginate(AdditionalServices::pagination_limit())
            ->appends($query_param);

        foreach ($customers as $customer) {
            $referralCount = $customer->referrals()->count();
            $customer->referral_count = $referralCount;
        }

        return view('admin-views.membership.list', compact('customers', 'search'));
    }


    public function delete($id)
    {
        $customer_membership = Membership::findOrFail($id);

        if ($customer_membership) {
            $customer = User::findOrFail($customer_membership->user_id);
            $customer->is_membership = false;
            $customer->save();
        }

        $customer_membership->delete();
        Toastr::success("Customer membership deleted successfully!", "Success");
        return redirect()->route('admin.membership.list');
    }


    private function downloadCsv($customers)
    {
        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->insertOne(['ID', 'First Name', 'Last Name', 'Phone', 'Email', 'Referral ID', 'Status', 'Point', 'Created At']);

        foreach ($customers as $customer) {
            $csv->insertOne([
                $customer->id,
                $customer->f_name,
                $customer->l_name,
                $customer->phone,
                $customer->email,
                $customer->membership->referral_id,
                $customer->membership->status,
                $customer->membership->points,
                $customer->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        $csv->output('membership.csv');
        exit;
    }




    public function show(string $user_id)
    {
        $customer = User::findOrFail($user_id);

        $customer->load('membership'); // Load the user's membership if it exists

        return view('admin-views.membership.membership-view', compact('customer'));
    }



    // Update Member Status
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'membership_status' => 'required|in:approved,pending,rejected',
        ]);

        $membership = Membership::findOrFail($id);
        $oldStatus = $membership->status;
        $membership->status = $request->membership_status;
        $membership->save();

        if ($membership->status === 'approved' && $oldStatus !== 'approved') {

            $user = $membership->user;
            $user->is_membership = true;
            $user->save();

            if ($user->phone) {
                $msg = "স্বজন এসএল পরিবারের পক্ষ থেকে আপনাকে স্বাগতম, \nআপনার নিত্য প্রয়োজনে আমাদের সাথে থাকুন ভেজালমুক্ত পণ্য কিনুন সুস্থ, স্বচ্ছল জীবন গড়ুন। \nধন্যবাদ, \nShojonsl.com";

                $res = SmsModule::sendSms_greenweb($user, $msg);
                Log::info('SMS Response = ' . $res);
            }

        } elseif ($membership->status === 'pending' && $oldStatus !== 'pending') {
            $user = $membership->user;
            $user->is_membership = false;
            $user->save();
        } elseif ($membership->status === 'rejected' && $oldStatus !== 'rejected') {
            $user = $membership->user;
            $user->is_membership = false;
            $user->save();
        }

        Toastr::success("Membership status updated successfully!", "Success");

        return Redirect::back();
    }


    // public function point_history($userId, Request $request)
    // {
    //     $search = $request->input('search');
    //     $from_date = $request->input('from_date');
    //     $to_date = $request->input('to_date');
    //     $status = $request->input('status');

    //     $query = PointHistory::with(['referredUser:id,f_name,l_name,email,phone'])
    //         ->where('user_id', $userId);

    //     // Apply search filter
    //     if ($search) {
    //         $query->where(function ($subQuery) use ($search) {
    //             $subQuery->whereHas('referredUser', function ($subSubQuery) use ($search) {
    //                 $subSubQuery->where('f_name', 'like', '%' . $search . '%')
    //                     ->orWhere('l_name', 'like', '%' . $search . '%')
    //                     ->orWhere('email', 'like', '%' . $search . '%')
    //                     ->orWhere('phone', 'like', '%' . $search . '%');
    //             })
    //                 ->orWhere('status', 'like', '%' . $search . '%');
    //         });
    //     }

    //     // Apply date range filter
    //     if ($from_date && $to_date) {
    //         $query->whereBetween('created_at', [$from_date, $to_date]);
    //     }

    //     if ($status) {
    //         $query->where('status', $status);
    //     }

    //     // Get the point histories
    //     $pointhistories = $query->get();

    //     // Check if CSV download is requested
    //     if ($request->has('download_csv')) {
    //         $this->point_downloadCsv($pointhistories);
    //     }

    //     // Paginate the results
    //     $pointhistories = $query->paginate(10);

    //     $user = User::find($userId);
    //     if (!$user) {
    //         abort(404, 'User not found');
    //     }
    //     $referredUsers = $user->referrals()->with('user:id,f_name,l_name,email,phone')->get();

    //     return view(
    //         'admin-views.membership.pointhistory_list',
    //         compact('pointhistories', 'search', 'from_date', 'to_date', 'userId', 'referredUsers')
    //     );
    // }


    public function point_history($userId, Request $request)
    {
        $search = $request->input('search');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $status = $request->input('status');

        $query = PointHistory::with(['referredUser:id,f_name,l_name,email,phone'])
            ->where('user_id', $userId);

        // Apply search filter
        if ($search) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->whereHas('referredUser', function ($subSubQuery) use ($search) {
                    $subSubQuery->where('f_name', 'like', '%' . $search . '%')
                        ->orWhere('l_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%');
                })
                    ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        // Apply date range filter
        if ($from_date && $to_date) {
            $query->whereBetween('created_at', [$from_date, $to_date]);
        }

        if ($status) {
            if ($status === 'self') {
                $query->where('status', 'self');
            } elseif ($status === 'referred') {
                $query->where('status', 'referred');
            } else {
                $query->where('referred_user', $status);
            }
        }

        // Get the point histories
        $pointhistories = $query->get();

        // Check if CSV download is requested
        if ($request->has('download_csv')) {
            $this->point_downloadCsv($pointhistories);
        }

        $totalPoints = $pointhistories->sum('points');
        $totalAmount = $pointhistories->sum('order_amount');

        // Paginate the results
        $pointhistories = $query->paginate(10);


        $user = User::find($userId);
        // Log::info("User =" . $user);
        $referredUsers = $user->referrals()->with('user:id,f_name,l_name,email,phone')->get();
        // Log::info("Reffered =" . $referredUsers);


        return view(
            'admin-views.membership.pointhistory_list',
            compact('pointhistories', 'search', 'from_date', 'to_date', 'userId', 'referredUsers', 'totalPoints', 'totalAmount')
        );
    }


    private function point_downloadCsv($pointhistories)
    {
        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->insertOne(['ID', 'Customername Info', 'Referrend Info', 'Status', 'Order Amount', 'Points', 'Created At']);

        $totalPoints = 0;
        $totalAmount = 0;

        foreach ($pointhistories as $pointhistory) {
            $user_name = $pointhistory->user->f_name . ' ' . $pointhistory->user->l_name;
            $user_email = $pointhistory->user->email;
            $user_phone = $pointhistory->user->phone;

            $user_info = $user_name . ', ' . $user_email . ', ' . $user_phone;

            $ref_name = $pointhistory->referredUser?->f_name . ' ' . $pointhistory->referredUser?->l_name;
            $ref_email = $pointhistory->referredUser?->email;
            $ref_phone = $pointhistory->referredUser?->phone;

            $referred_in = $ref_name . ', ' . $ref_email . ', ' . $ref_phone;
            $referred_info = $referred_in;

            $csv->insertOne([
                $pointhistory->id,
                $user_info,
                $referred_info,
                $pointhistory->status,
                $pointhistory->order_amount,
                $pointhistory->points,
                $pointhistory->created_at,
            ]);

            $totalPoints += $pointhistory->points;
            $totalAmount += $pointhistory->order_amount;
        }

        // Insert the total points and total amount row
        $csv->insertOne(['', '', '', '', "Total = " . $totalAmount, "Total = " . $totalPoints, '']);

        $csv->output('point_history.csv');
        exit;
    }




}
