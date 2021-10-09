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
                'name' => 'Administrador',
                'code' => 'ADMIN',
                'role' => 'ADMINISTRADOR',
                'group' => 0,
                'order' => 1000,
            ], [
                'name' => 'Presidencia',
                'code' => 'PRE',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 1,
            ], [
                'name' => 'Vice Presidencia',
                'code' => 'VPRE',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 2,
            ], [
                'name' => 'Stria. Régimen Interno y Personal',
                'code' => 'SRIP',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 3,
            ], [
                'name' => 'Secretaría Hacienda',
                'code' => 'SHA',
                'role' => 'SECRETARÍA',
                'group' => 2,
                'order' => 4,
            ], [
                'name' => 'Secretaría General',
                'code' => 'SGE',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 5,
            ], [
                'name' => 'Stria. Bienestar Social y Vivienda',
                'code' => 'SBSV',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 6,
            ], [
                'name' => 'Stria. de Educación y Cultura',
                'code' => 'SEC',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 7,
            ], [
                'name' => 'Stria. de Actas y Deporte',
                'code' => 'SAD',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 8,
            ], [
                'name' => 'Director Administrativo',
                'code' => 'DADM',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 9,
            ], [
                'name' => 'Unidad de Sistemas',
                'code' => 'USIS',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 10,
            ], [
                'name' => 'Unidad de Asesoría Jurídica',
                'code' => 'UAJ',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 11,
            ], [
                'name' => 'Unidad de Auditoría',
                'code' => 'UAUD',
                'role' => 'SECRETARÍA',
                'group' => 1,
                'order' => 12,
            ], [
                'name' => 'Responsable de Prestaciones',
                'code' => 'RPRE',
                'role' => 'SECRETARÍA',
                'group' => 2,
                'order' => 13,
            ], [
                'name' => 'Atención en Plataforma',
                'code' => 'APL',
                'role' => 'VERIFICADOR',
                'group' => 2,
                'order' => 14,
            ], [
                'name' => 'Tesorería',
                'code' => 'TES',
                'role' => 'SECRETARÍA',
                'group' => 2,
                'order' => 15,
            ], [
                'name' => 'Stria. Relaciones Publicas, Prensa y Propaganda',
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
