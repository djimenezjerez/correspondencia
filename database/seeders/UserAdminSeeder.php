<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class UserAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::firstOrCreate([
            'name' => 'ADMINISTRATOR',
        ], []);

        $permissions = [
            'CREATE USER',
            'READ USER',
            'UPDATE USER',
            'DELETE USER',
            'READ ROLE',
        ];

        foreach($permissions as $permission) {
            $role->permissions()->firstOrCreate([
                'name' => $permission,
            ], []);
        }


        $role->givePermissionTo($permissions);

        $user = User::firstOrCreate([
            'username' => 'admin',
        ], [
            'name' => 'ADMINISTRATOR',
            'password' => 'admin',
        ]);

        $user->assignRole($role);
    }
}
