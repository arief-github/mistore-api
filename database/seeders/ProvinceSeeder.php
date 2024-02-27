<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Fetch REST API
        $response = Http::withHeaders([
            // api key from raja ongkir
            'key' => config('services.rajaongkir.key'),
        ])->withOptions([
            'debug' => true,
            'verifiy_host' => false,
        ])->get('https://api.rajaongkir.com/starter/province');

        // looping data from rest API
        foreach ($response['rajaongkir']['results'] as $province) {
            // insert into province table
            Province::create([
                'province_id' => $province['province_id'],
                'name' => $province['province']
            ]);
        }
    }
}
