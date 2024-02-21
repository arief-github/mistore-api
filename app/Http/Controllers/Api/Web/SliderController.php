<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use App\Http\Resources\SliderResource;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource
     */

    public function index()
    {
        // get sliders
        $sliders = Slider::latest()->get();

        // return with api Resource
        return new SliderResource(true, 'List Data Sliders', $sliders);
    }
}
