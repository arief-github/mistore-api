<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource
     *
     */

    public function index()
    {
        // get categories
        $categories = Category::when(request()->q, function($categories) {
            $categories = $categories->where('name', 'like', '%'.request()->q. '%');
        })->latest()->paginate(5);

        // return with API Resource
        return new CategoryResource(true, 'List Data Categories', $categories);
    }

    /**
     *  Store the new Category
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "image" => "required|image|mimes:jpeg,jpg,png|max:2000",
            "name" => "required|unique:categories",
        ]);

        if ($validator->fails()) {
           return response()->json($validator->errors(), 422);
        }

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/categories', $image->hashName());

        // create category
        $category = Category::create([
            'image' => $image->hashName(),
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-')
        ]);

        if (!$category) {
            // return failed with Api Resource
            return new CategoryResource(false, 'Data Category Gagal Disimpan!', null);
        }

        // return success with Api Resource
        return new CategoryResource(true, 'Data Category Berhasil Disimpan!', $category);
    }

    /**
     * Show specific category
     */

    public function show($id)
    {
        $category = Category::whereId($id)->first();

        if(!$category) {
            // return failed with API resource
            return new CategoryResource(false, 'Detail Data Category Tidak Ditemukan!', null);
        }

        // return sucess with API resource
        return new CategoryResource(true, 'Detail Data Category Ditemukan!', $category);
    }

    /**
     * Update spesific category data
     */

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name,'.$category->id,
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // check image update, it ensure category update with image
        if ($request->file('image')) {
            // remove old image
            Storage::disk('local')->delete('public/categories/'.basename($category->image));

            // upload new image
            $image = $request->file('image');
            $image->storeAs('public/categories', $image->hashName());

            // update category with new image
            $category->update([
                'image' => $image->hashName(),
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-'),
            ]);
        }

        // if admin update category without image
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-')
        ]);

        if($category) {
            // return success with API resource
            return new CategoryResource(true, 'Data Category Berhasil Diupdate!', $category);
        }

        //return failed with API resource
        return new CategoryResource(false, 'Data Category Gagal diUpdate!', null);
    }

    public function destroy(Category $category)
    {
        // remove image
        Storage::disk('local')->delete('public/categories/'.basename($category->image));

        if ($category->delete()) {
            // return success with API resource
            return new CategoryResource(true, 'Data Category Berhasil Dihapus!', null);
        }

        // return failed condition with API resource
        return new CategoryResource(false, 'Data Category Gagal Dihapus!', null);
    }
}
