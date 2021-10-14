<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'ADMINISTRADOR',
                'permissions' => [
                    'LEER USUARIO',
                    'CREAR USUARIO',
                    'EDITAR USUARIO',
                    'ELIMINAR USUARIO',
                    'LEER ROL',
                    'CREAR REQUISITO',
                    'EDITAR REQUISITO',
                    'ELIMINAR REQUISITO',
                    'CREAR TIPO TRÁMITE',
                    'EDITAR TIPO TRÁMITE',
                    'ELIMINAR TIPO TRÁMITE',
                ],
            ], [
                'name' => 'VERIFICADOR',
                'permissions' => [
                    'CREAR TRÁMITE',
                    'LEER TRÁMITE',
                    'EDITAR TRÁMITE',
                    'ELIMINAR TRÁMITE',
                    'DERIVAR TRÁMITE',
                    'ARCHIVAR TRÁMITE',
                    'ADJUNTAR ARCHIVO',
                    'LEER REQUISITOS DE TRÁMITE',
                    'EDITAR REQUISITOS DETRÁMITE',
                ],
            ], [
                'name' => 'SECRETARÍA',
                'permissions' => [
                    'CREAR TRÁMITE',
                    'LEER TRÁMITE',
                    'EDITAR TRÁMITE',
                    'ELIMINAR TRÁMITE',
                    'DERIVAR TRÁMITE',
                    'ARCHIVAR TRÁMITE',
                    'ADJUNTAR ARCHIVO',
                ],
            ],
        ];

        foreach($roles as $role) {
            $new_role = Role::firstOrCreate([
                'name' => $role['name'],
            ]);

            foreach($role['permissions'] as $permission) {
                $new_permission = Permission::where('name', $permission)->first();
                if (!$new_permission) {
                    $new_permission = Permission::create([
                        'name' => $permission,
                    ]);
                }
                $new_role->givePermissionTo($new_permission);
            }
        }
    }
}
