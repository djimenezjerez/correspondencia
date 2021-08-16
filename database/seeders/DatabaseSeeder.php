<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Unguarding models');
        Model::unguard();
        $this->call(RolesSeeder::class);
        $this->call(AreasSeeder::class);
        $this->call(DocumentTypesSeeder::class);
        $this->call(UserAdminSeeder::class);
        $this->call(RequirementsSeeder::class);
        $this->call(ProcedureTypeSeeder::class);
    }
}
