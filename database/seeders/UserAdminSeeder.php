<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserAdminSeeder extends Seeder
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
            'password' => Hash::make('admin'),
        ]);

        $role = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'api'
        ], []);

        $user->assignRole($role);
    }
}
