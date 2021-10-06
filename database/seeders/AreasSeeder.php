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
                'order' => 1000,
            ], [
                'name' => 'PRESIDENCIA',
                'code' => 'PRE',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 1,
            ], [
                'name' => 'VICE PRESIDENCIA',
                'code' => 'VPRE',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 2,
            ], [
                'name' => 'STRIA. RÉGIMEN INTERNO Y PERSONAL',
                'code' => 'SRIP',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 3,
            ], [
                'name' => 'SECRETARÍA HACIENDA',
                'code' => 'SHA',
                'role' => 'SECRETARÍA',
                'group' => 2,
                'order' => 4,
            ], [
                'name' => 'SECRETARÍA GENERAL',
                'code' => 'SGE',
                'role' => 'RECEPCIÓN',
                'group' => 1,
                'order' => 5,
            ], [
                'name' => 'STRIA. BIENESTAR SOCIAL Y VIVIENDA',
                'code' => 'SBSV',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 6,
            ], [
                'name' => 'STRIA. DE EDUCACIÓN Y CULTURA',
                'code' => 'SEC',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 7,
            ], [
                'name' => 'STRIA. DE ACTAS Y DEPORTE',
                'code' => 'SAD',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 8,
            ], [
                'name' => 'DIRECTOR ADMINISTRATIVO',
                'code' => 'DADM',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 9,
            ], [
                'name' => 'UNIDAD DE SISTEMAS',
                'code' => 'USIS',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 10,
            ], [
                'name' => 'UNIDAD DE ASESORÍA JURÍDICA',
                'code' => 'UAJ',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 11,
            ], [
                'name' => 'UNIDAD DE AUDITORÍA',
                'code' => 'UAUD',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 12,
            ], [
                'name' => 'RESPONSABLE DE PRESTACIONES',
                'code' => 'RPRE',
                'role' => 'SECRETARÍA',
                'group' => 2,
                'order' => 13,
            ], [
                'name' => 'ATENCIÓN EN PLATAFORMA',
                'code' => 'APL',
                'role' => 'VERIFICADOR',
                'group' => 2,
                'order' => 14,
            ], [
                'name' => 'TESORERÍA',
                'code' => 'TES',
                'role' => 'SECRETARÍA',
                'group' => 2,
                'order' => 15,
            ], [
                'name' => 'STRIA. RELACIONES PUBLICAS, PRENSA Y PROPAGANDA',
                'code' => 'SRPPP',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 16,
            ],
        ];

        foreach($areas as $area) {
            $role = Role::where('name', $area['role'])->firstOrFail();
            $new_area = Area::firstOrCreate([
                'code' => $area['code'],
            ], [
                'name' => $area['name'],
                'group' => $area['group'],
                'order' => $area['order'],
                'role_id' => $role->id,
            ]);
        }
    }
}
