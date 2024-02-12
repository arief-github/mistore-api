<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use App\Http\Resources\SliderResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    /**
     * Display a listing of resource
     *
     */

    public function index()
    {
        // get sliders
        $sliders = Slider::latest()->paginate(5);

        // return with Api Resource
        return new SliderResource(true, 'List Data Sliders', $sliders);
    }
    /**
     * Store a newly created resource in storage*
     *
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2000'
        ]);

        if ($validator->fails()) {
           return response()->json($validator->errors(), 422);
        }

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/sliders', $image->hashName());

        // create slider
        $slider = Slider::create([
            'image' => $image->hashName(),
            'link' => $request->link
        ]);

        if (!$slider) {
            return new SliderResource(false, 'Data Slider Gagal Disimpan', null);
        }

        return new SliderResource(true, 'Data Slider Berhasil Disimpan', $slider);
    }

    // Remove Specific Resource
    public function destroy(Slider $slider)
    {
        // remove image
        Storage::disk('local')->delete('public/sliders/'.basename($slider->image));

        if($slider->delete()) {
            // return success with apiResource
            return new SliderResource(true, 'Data Slider Berhasil Dihapus!', null);
        }

        // return failed with apiResource
        return new SliderResource(false, 'Data Slider Gagal Dihapus', null);
    }
}
