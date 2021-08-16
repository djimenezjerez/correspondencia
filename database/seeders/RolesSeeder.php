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
                    'CREAR USUARIO',
                    'LEER USUARIO',
                    'ACTUALIZAR USUARIO',
                    'ELIMINAR USUARIO',
                    'LEER ROL',
                    'LEER ÁREA',
                    'LEER TIPO DOCUMENTO',
                ],
            ], [
                'name' => 'RECEPCIÓN',
                'permissions' => [
                    'CREAR TRÁMITE',
                    'LEER TRÁMITE',
                    'ACTUALIZAR TRÁMITE',
                    'ELIMINAR TRÁMITE',
                    'DERIVAR TRÁMITE',
                ],
            ], [
                'name' => 'SECRETARÍA',
                'permissions' => [
                    'DERIVAR TRÁMITE',
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
