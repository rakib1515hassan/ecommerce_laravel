<?php

namespace App\Http\Controllers\Admin;

use App\Services\AdditionalServices;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubSubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $categories = Category::where(['position'=>2])->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }else{
            $categories=Category::where(['position'=>2]);
        }
        $categories = $categories->latest()->paginate(AdditionalServices::pagination_limit())->appends($query_param);
        return view('admin-views.category.sub-sub-category-view',compact('categories','search'));
    }

    public function store(Request $request)
    {
        $category = new Category;
        $category->name = $request->name[array_search('en', $request->lang)];
        $category->slug = Str::slug($request->name[array_search('en', $request->lang)]);
        $category->parent_id = $request->parent_id;
        $category->position = 2;
        $category->save();
        foreach($request->lang as $index=>$key)
        {
            if($request->name[$index] && $key != 'en')
            {
                Translation::updateOrInsert(
                    ['translationable_type'  => 'App\Models\Category',
                        'translationable_id'    => $category->id,
                        'locale'                => $key,
                        'key'                   => 'name'],
                    ['value'                 => $request->name[$index]]
                );
            }
        }
        Toastr::success('Sub Sub Category updated successfully!');
        return back();
    }

    public function edit(Request $request)
    {
        $data = Category::where('id',$request->id)->first();
        return response()->json($data);
    }
    public function update(Request $request)
    {
        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->parent_id = $request->parent_id;
        $category->position = 2;
        $category->save();
        return response()->json();
    }
    public function delete(Request $request)
    {
        $translation = Translation::where('translationable_type','App\Models\Category')
                                    ->where('translationable_id',$request->id);
        $translation->delete();
        Category::destroy($request->id);
        return response()->json();
    }
    public function fetch(Request $request){
        if($request->ajax())
        {
            $data = Category::where('position',2)->orderBy('id','desc')->get();
            return response()->json($data);
        }
    }

    public function getSubCategory(Request $request)
    {
        $data = Category::where("parent_id",$request->id)->get();
        $output="";
        foreach($data as $row)
        {
            $output .= '<option value="'.$row->id.'">'.$row->name.'</option>';
        }
        echo $output;
    }

    public function getCategoryId(Request $request)
    {
        $data= Category::where('id',$request->id)->first();
        return response()->json($data);
    }
}
