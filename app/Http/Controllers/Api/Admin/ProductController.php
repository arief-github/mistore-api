<?php

namespace App\Http\Controllers\Api\Admin;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource
     *
     */

    public function index()
    {
        //get products
        $products = Product::with('category')
            //count and average
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            //search
            ->when(request()->q, function($products) {
                $products = $products->where('title', 'like', '%'. request()->q . '%');
            })->latest()->paginate(8);

        //return with Api Resource
        return new ProductResource(true, 'List Data Products', $products);
    }

    /**
     * Store a newly created resource in storage
     *
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'title' => 'required|unique:products',
            'category_id' => 'required',
            'description' => 'required',
            'weight' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'discount' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // upload image for product
        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        // create product
        $product = Product::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'slug' => Str::slug($request->title, '-'),
            'category_id' => $request->category_id,
            'user_id' => auth()->guard('api_admin')->user()->id,
            'description' => $request->description,
            'weight' => $request->weight,
            'price' => $request->price,
            'stock' => $request->stock,
            'discount' => $request->discount
        ]);

        if($product) {
            // return failed with Api Resource
            return new ProductResource(true, 'Data Product Berhasil Disimpan!', $product);
        }

        // return success with Api Resource
        return new ProductResource(false, 'Data Product Gagal Disimpan!', null);
    }

    /**
     * Display the spesific resource
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $product = Product::whereId($id)->first();

        if (!$product) {
            // return failed with API resource
            return new ProductResource(false, 'Detail Data Product Tidak Ditemukan!', null);
        }

        // return success with API resource
        return new ProductResource(true, 'Detail Data Product Ditemukan!', $product);
    }

    /**
     * Update the specified resource in storage
     *
     */

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:products,title,'.$product->id,
            'category_id' => 'required',
            'description' => 'required',
            'weight' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'discount' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // check for image update
        if($request->file('image')) {
            // remove old image
            Storage::disk('local')->delete('public/products/'.basename($product->image));

            // upload new image
            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());

            // upload product with new image
            $product->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'slug' => Str::slug($request->title, '-'),
                'category_id' => $request->category_id,
                'user_id' => auth()->guard('api_admin')->user()->id,
                'description' => $request->description,
                'weight' => $request->weight,
                'price' => $request->price,
                'stock' => $request->stock,
                'discount' => $request->discount
            ]);
        }

        // update product without image
        $product->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title, '-'),
            'category_id' => $request->category_id,
            'user_id' => auth()->guard('api_admin')->user()->id,
            'description' => $request->description,
            'weight' => $request->weight,
            'price' => $request->price,
            'stock' => $request->stock,
            'discount' => $request->discount
        ]);

        if(!$product) {
            // return failed with api resource
            return new ProductResource(false, "Data Product Gagal Diupdate!", null);
        }

        return new ProductResource(true, 'Data Product Berhasil Diupdate!', $product);
    }

    /**
     * Remove the specified resource from storage
     *
     */

    public function destroy(Product $product)
    {
        // remove image
        Storage::disk('local')->delete('public/products/'.basename($product->image));

        if($product->delete()) {
            // return success with API resource
            return new ProductResource(true, 'Data Product Berhasil Dihapus!', null);
        }

        // return failed with API resource
        return new ProductResource(false, "Data Product Gagal Dihapus", null);
    }
}
