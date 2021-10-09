<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Area;
use Illuminate\Support\Facades\Storage;
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
        $file = 'admin_data.json';
        if (!Storage::has($file)) {
            throw new \RuntimeException('Primero debe establecer los datos del administrador');
        } else {
            $data = json_decode(Storage::get($file), true);
            $area = Area::where('name', 'Administrador')->firstOrFail();
            $user = User::firstOrCreate([
                'username' => $data['username'],
            ], [
                'name' => $data['name'],
                'last_name' => $data['last_name'],
                'identity_card' => $data['identity_card'],
                'password' => $data['password'],
                'email' => $data['email'],
                'address' => $data['address'],
                'phone' => $data['phone'],
                'area_id' => $area->id,
            ]);
            $user->syncRoles([$area->role_id]);
            Storage::delete($file);
        }
    }
}
