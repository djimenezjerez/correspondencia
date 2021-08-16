<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
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
                'name' => 'ADMINISTRADOR',
                'code' => 'ADMIN',
                'role' => 'ADMINISTRADOR',
            ], [
                'name' => 'SECRETARÍA GENERAL',
                'code' => 'SG',
                'role' => 'RECEPCIÓN',
            ], [
                'name' => 'SECRETARÍA HACIENDA',
                'code' => 'SH',
                'role' => 'SECRETARÍA',
            ], [
                'name' => 'RESPONSABLE DE PRESTACIONES',
                'code' => 'RP',
                'role' => 'SECRETARÍA',
            ], [
                'name' => 'ATENCION EN PLATAFORMA',
                'code' => 'AP',
                'role' => 'VERIFICADOR',
            ], [
                'name' => 'SECRETARÍA PRESIDENCIA',
                'code' => 'SPR',
                'role' => 'SECRETARÍA',
            ], [
                'name' => 'SECRETARÍA VICE PRESIDENCIA',
                'code' => 'SVP',
                'role' => 'SECRETARÍA',
            ], [
                'name' => 'SECRETARÍA PERSONAL',
                'code' => 'SPE',
                'role' => 'SECRETARÍA',
            ], [
                'name' => 'SECRETARÍA BIENESTAR SOCIAL Y VIVIENDA',
                'code' => 'SBSV',
                'role' => 'SECRETARÍA',
            ], [
                'name' => 'SECRETARÍA DE EDUCACIÓN',
                'code' => 'SE',
                'role' => 'SECRETARÍA',
            ],
        ];

        foreach($areas as $area) {
            $role = Role::where('name', $area['role'])->firstOrFail();
            $new_area = Area::firstOrCreate([
                'code' => $area['code'],
            ], [
                'name' => $area['name'],
                'role_id' => $role->id,
            ]);
        }
    }
}
