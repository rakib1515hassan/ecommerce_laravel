<?php

namespace App\Http\Controllers\Admin;

use App\Services\AdditionalServices;
use App\Services\ImageManager;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Brand;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Translation;

class BrandController extends Controller
{
    public function add_new()
    {
        $br = Brand::latest()->paginate(AdditionalServices::pagination_limit());
        return view('admin-views.brand.add-new', compact('br'));
    }

    public function store(Request $request)
    {
        $brand = new Brand;
        $brand->name = $request->name[array_search('en', $request->lang)];
        $brand->slug = slugify($request->name[array_search('en', $request->lang)]);
        $brand->image = ImageManager::upload('brand/', 'png', $request->file('image'));
        $brand->status = 1;
        $brand->save();

        foreach($request->lang as $index=>$key)
        {
            if($request->name[$index] && $key != 'en')
            {
                Translation::updateOrInsert(
                    ['translationable_type'  => 'App\Models\Brand',
                        'translationable_id'    => $brand->id,
                        'locale'                => $key,
                        'key'                   => 'name'],
                    ['value'                 => $request->name[$index]]
                );
            }
        }
        Toastr::success('Brand added successfully!');
        return back();
    }

    function list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $br = Brand::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }else{
            $br = new Brand();
        }
        $br = $br->latest()->paginate(AdditionalServices::pagination_limit())->appends($query_param);
        return view('admin-views.brand.list', compact('br','search'));
    }

    public function edit($id)
    {
        $b = Brand::where(['id' => $id])->withoutGlobalScopes()->first();
        return view('admin-views.brand.edit', compact('b'));
    }

    public function update(Request $request, $id)
    {

        $brand = Brand::find($id);
        $brand->name = $request->name[array_search('en', $request->lang)];
        $brand->slug = slugify($request->name[array_search('en', $request->lang)]);
        if ($request->has('image')) {
            $brand->image = ImageManager::update('brand/', $brand['image'], 'png', $request->file('image'));
         }
        $brand->save();
        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != 'en') {
                Translation::updateOrInsert(
                    ['translationable_type' => 'App\Models\Brand',
                        'translationable_id' => $brand->id,
                        'locale' => $key,
                        'key' => 'name'],
                    ['value' => $request->name[$index]]
                );
            }
        }

        Toastr::success('Brand updated successfully!');
        return back();
    }

    public function delete(Request $request)
    {
        $translation = Translation::where('translationable_type','App\Models\Brand')
                                    ->where('translationable_id',$request->id);
        $translation->delete();
        $brand = Brand::find($request->id);
        ImageManager::delete('/brand/' . $brand['image']);
        $brand->delete();
        return response()->json();
    }
}
