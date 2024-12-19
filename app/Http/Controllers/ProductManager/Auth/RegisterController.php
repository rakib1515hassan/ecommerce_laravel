<?php

namespace App\Http\Controllers\ProductManager\Auth;

use App\Models\Seller;
use Illuminate\Http\Request;
use App\Models\ProductManager;
use App\Services\ImageManager;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Mail\RegistrationNotification;
use Illuminate\Support\Facades\Mail;


class RegisterController extends Controller
{
    public function create()
    {
        $sellers = Seller::all();
        return view('product_manager-views.auth.register', compact('sellers'));
    }


    // public function store(Request $request,$sellerId, $productManagerId)
    public function store(Request $request)
    {

        dd($request->all());

        $request->validate([
            'seller_id' => 'required',
            'f_name' => 'required',
            'l_name' => 'required',
            'password' => 'required|min:8|confirmed',
            'email' => 'required',
            'phone' => 'required|unique:product_managers',
        ], [
            'f_name.required' => 'First name is required!'
        ]);

        $product_manager = ProductManager::where(['email' => $request['email'], 'seller_id' =>  $request['seller_id']])->first();
        $product_manager_phone = ProductManager::where(['phone' => $request['phone'], 'seller_id' =>  $request['seller_id']])->first();

        if (isset($product_manager)) {
            $request->validate([
                'email' => 'required|unique:product_managers',
            ]);
        }

        if (isset($product_manager_phone)) {
            $request->validate([
                'phone' => 'required|unique:product_managers',
            ]);
        }

        $id_img_names = [];
        if (!empty($request->file('identity_image'))) {
            foreach ($request->identity_image as $img) {
                array_push($id_img_names, ImageManager::upload('product_manager/', 'png', $img));
            }
            $identity_image = json_encode($id_img_names);
        } else {
            $identity_image = json_encode([]);
        }

        $dm = new ProductManager();
        // $dm->seller_id = implode(',', $request->input('seller_id'));
        $dm->seller_id = $request->seller_id;
        $dm->f_name = $request->f_name;
        $dm->l_name = $request->l_name;
        $dm->email = $request->email;
        $dm->phone = $request->phone;
        $dm->identity_number = $request->identity_number;
        $dm->identity_type = $request->identity_type;
        $dm->image = ImageManager::upload('product_manager/', 'png', $request->file('image'));
        $dm->password = bcrypt($request->password);
        $dm->save();


        // $seller = ProductManager::with('seller')->find($sellerId);
        // $product_manager = ProductManager::find($productManagerId);
        // $productManagerName = $product_manager->f_name;
        // $productManagerEmail = $product_manager->email;
        // $sellerName = $seller->seller->f_name;
        // $sellerEmail = $seller->seller->email;

        // Mail::to($sellerEmail)
        // ->send(new RegistrationNotification(compact('sellerName', 'productManagerName', 'productManagerEmail')));


        Toastr::success('Registration Apply successfully!! Please, wait for the seller approval.');
        return redirect()->route('product_manager.auth.login');
    }
}
