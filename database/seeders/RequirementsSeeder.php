<?php

namespace Database\Seeders;

use App\Models\Requirement;
use Illuminate\Database\Seeder;

class RequirementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $requirements = [
            'BOLETA DE PAGO DE LOS 3 ÚLTIMOS MESES',
            'FOTOCOPIA DE CÉDULA DE IDENTIDAD',
            'CERTIFICADO DE NACIMIENTO',
        ];

        foreach($requirements as $requirement) {
            Requirement::firstOrCreate([
                'name' => $requirement,
            ], []);
        }
    }
}
