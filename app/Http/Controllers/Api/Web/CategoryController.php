<?php

namespace App\Http\Controllers\Api\Web;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of resource in public
     *
     */
    public function index()
    {
        // get categories
        $categories = Category::latest()->get();

        // return with api resource
        return new CategoryResource(true, 'List Data Categories', $categories);
    }

    /**
     * Display the specified resource
     *
     */

    public function show($slug)
    {
        $category = Category::with('products.category')
            // get count review and average review
            ->with('products')
            ->where('slug', $slug)->first();

            if(!$category) {
                // return failed with API Resource
                return new CategoryResource(false, 'Detail Data Category Tidak Ditemukan!', null);
            }

            return new CategoryResource(true, 'Data Product By Category : '.$category->name.'', $category);
    }

}
