<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    /**
     * Display listing of the resource
     */

    public function index()
    {
        // get products
        $products = Product::with('category')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->when(request()->q, function($products) {
                $products = $products->where('title', 'like', '%'.
                    request()->q . '%');
            })->latest()->paginate(8);


        // return with API resource
        return new ProductResource(true, 'List Data Products', $products);
    }
    /**
     * Display the specified resource
     */
    public function show($slug) {
        $product = Product::with('category')
            ->where('slug', $slug)->first();

        if ($product) {
            return new ProductResource(true, 'Detail Data Product', $product);
        }

        // return failed with API Resource
        return new ProductResource(false, 'Detail Data Product Tidak Ditemukan', null);
    }
}
