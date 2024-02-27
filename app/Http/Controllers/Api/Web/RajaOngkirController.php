<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\RajaOngkirResource;

class RajaOngkirController extends Controller
{
    /**
     * get Provinces list
     */

    public function getProvinces()
    {
        // get all provinces
        $province = Province::all();

        // return api resource
        return new RajaOngkirResource(true, 'List Data Provinsi Berhasil Ditampilkan', $province);
    }

    /**
     * getCities
     *
     */
    public function getCities(Request $request)
    {
        // get province name
        $province = Province::where('province_id', $request->province_id)->first();

        // get cities by province
        $cities = City::where('province_id', $request->province_id)->get();

        // return with api resource
        return new RajaOngkirResource(true, 'List Data City By Province : '.$province->name. '', $cities);
    }
    /**
     * Check ongkir (Ongkos Kirim)
     */

    public function checkOngkir(Request $request)
    {
        // fetch rest API
        $response = Http::withHeaders([
            'key' => config('services.rajaongkir.key')
        ])->post('https://api.rajaongkir.com/starter/cost', [
            // send data
            'origin' => 113,
            'destination' => $request->destination,
            'weight' => $request->weight,
            'courier' => $request->courier
        ]);

        // return with api resource
        return new RajaOngkirResource(true, 'List Biaya Ongkos Kirim : '.$request->courier.'', $response['rajaongkir']['results'][0]['costs']);
    }
}
