<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $areas = [
            [
                'name' => 'ENCARGADO DE SISTEMAS',
                'code' => 'EDS',
            ], [
                'name' => 'SECRETARÍA DE PRESIDENCIA',
                'code' => 'SDP',
            ], [
                'name' => 'SECRETARÍA DE HACIENDA',
                'code' => 'SDH',
            ],
        ];

        foreach($areas as $area) {
            Area::firstOrCreate([
                'code' => $area['code'],
            ], [
                'name' => $area['name'],
            ]);
        }
    }
}
