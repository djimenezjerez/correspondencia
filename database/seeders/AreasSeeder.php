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
                'group' => 0,
                'order' => 1,
            ], [
                'name' => 'SECRETARÍA GENERAL',
                'code' => 'SG',
                'role' => 'RECEPCIÓN',
                'group' => 1,
                'order' => 2,
            ], [
                'name' => 'SECRETARÍA HACIENDA',
                'code' => 'SH',
                'role' => 'SECRETARÍA',
                'group' => 2,
                'order' => 3,
            ], [
                'name' => 'RESPONSABLE DE PRESTACIONES',
                'code' => 'RP',
                'role' => 'SECRETARÍA',
                'group' => 2,
                'order' => 4,
            ], [
                'name' => 'ATENCION EN PLATAFORMA',
                'code' => 'AP',
                'role' => 'VERIFICADOR',
                'group' => 2,
                'order' => 5,
            ], [
                'name' => 'TESORERÍA',
                'code' => 'TE',
                'role' => 'SECRETARÍA',
                'group' => 2,
                'order' => 6,
            ], [
                'name' => 'SECRETARÍA PRESIDENCIA',
                'code' => 'SPR',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 7,
            ], [
                'name' => 'SECRETARÍA VICE PRESIDENCIA',
                'code' => 'SVP',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 8,
            ], [
                'name' => 'SECRETARÍA PERSONAL',
                'code' => 'SPE',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 9,
            ], [
                'name' => 'SECRETARÍA BIENESTAR SOCIAL Y VIVIENDA',
                'code' => 'SBSV',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 10,
            ], [
                'name' => 'SECRETARÍA DE EDUCACIÓN',
                'code' => 'SE',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 11,
            ],
        ];

        foreach($areas as $area) {
            $role = Role::where('name', $area['role'])->firstOrFail();
            $new_area = Area::firstOrCreate([
                'code' => $area['code'],
            ], [
                'name' => $area['name'],
                'group' => $area['group'],
                'role_id' => $role->id,
            ]);
        }
    }
}
