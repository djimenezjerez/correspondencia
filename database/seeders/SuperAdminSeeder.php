<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::firstOrCreate([
            'username' => 'admin',
        ], [
            'name' => 'Administrator',
            'password' => 'admin',
        ]);

        $role = Role::firstOrCreate([
            'name' => 'Super Admin',
        ], []);

        $user->assignRole($role);

        $permissions = [
            'Create User',
            'Read User',
            'Update User',
            'Delete User',
        ];

        foreach($permissions as $role) {
            Permission::firstOrCreate([
                'name' => $role,
            ], []);
        }

        $role = Role::firstOrCreate([
            'name' => 'Administrator',
        ], []);

        $role->givePermissionTo($permissions);
    }
}
