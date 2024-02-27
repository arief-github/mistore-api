<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use Illuminate\Support\Facades\Http;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $response = Http::withHeaders([
            // api key from raja ongkir
            'key' => config('services.rajaongkir.key'),
        ])->get('https://api.rajaongkir.com/starter/city');

        // looping data from rest API
        foreach ($response['rajaongkir']['results'] as $city) {
            // insert into province table
            City::create([
                'province_id' => $city['province_id'],
                'city_id' => $city['city_id'],
                'name' => $city['city_name'] . ' - ' . '(' . $city['type'] . ')',
            ]);
        }
    }
}
