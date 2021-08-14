<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use App\Models\User;
use App\Models\Area;
use Spatie\Permission\Models\Role;
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
            $role = Role::where('name', 'ADMINISTRADOR')->firstOrFail();
            $document_type = DocumentType::where('code', $data['document_type'])->firstOrFail();
            $area = Area::where('name', $data['area'])->firstOrFail();
            $user = User::firstOrCreate([
                'username' => $data['username'],
            ], [
                'name' => $data['name'],
                'password' => $data['password'],
                'email' => $data['email'],
                'address' => $data['address'],
                'phone' => $data['phone'],
                'document_type_id' => $document_type->id,
                'area_id' => $area->id,
            ]);
            $user->assignRole($role);
            Storage::delete($file);
        }
    }
}
