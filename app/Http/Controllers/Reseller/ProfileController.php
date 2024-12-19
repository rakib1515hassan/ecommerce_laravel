<?php

namespace App\Http\Controllers\Reseller;

use App\Services\ImageManager;
use App\Http\Controllers\Controller;
use App\Models\Reseller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;


class ProfileController extends Controller
{
    public function view()
    {
        $data = Reseller::where('id', auth('reseller')->id())->first();
        return view('reseller-views.profile.view', compact('data'));
    }

    public function edit($id)
    {
        if (auth('reseller')->id() != $id) {
            Toastr::warning(translate('you_can_not_change_others_profile'));
            return back();
        }
        $data = Reseller::where('id', auth('reseller')->id())->first();
        return view('reseller-views.profile.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $reseller = Reseller::find(auth('reseller')->id());
        $reseller->f_name = $request->f_name;
        $reseller->l_name = $request->l_name;
        $reseller->phone = $request->phone;
        if ($request->image) {
            $reseller->image = ImageManager::update('reseller/', $reseller->image, 'png', $request->file('image'));
        }
        $reseller->save();

        Toastr::info('Profile updated successfully!');
        return back();
    }

    public function settings_password_update(Request $request)
    {
        $request->validate([
            'password' => 'required|same:confirm_password|min:8',
            'confirm_password' => 'required',
        ]);

        $reseller = Reseller::find(auth('reseller')->id());
        $reseller->password = bcrypt($request['password']);
        $reseller->save();
        Toastr::success('reseller password updated successfully!');
        return back();
    }

    public function bank_update(Request $request, $id)
    {
        $bank = Reseller::find(auth('reseller')->id());
        $bank->bank_name = $request->bank_name;
        $bank->branch = $request->branch;
        $bank->holder_name = $request->holder_name;
        $bank->account_no = $request->account_no;
        $bank->save();
        Toastr::success('Bank Info updated');
        return redirect()->route('reseller.profile.view');
    }

    public function bank_edit($id)
    {
        if (auth('reseller')->id() != $id) {
            Toastr::warning(translate('you_can_not_change_others_info'));
            return back();
        }
        $data = Reseller::where('id', auth('reseller')->id())->first();
        return view('reseller-views.profile.bankEdit', compact('data'));
    }

}
