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
            'FOTOCOPIA BOLETA DE PAGO (MES ANTERIOR)',
            'EXTRACTO BANCARIO',
            'FOTOCOPIA CARNET DE IDENTIDAD',
            'FOTOCOPIA CARNET MILITAR',
            'FOTOCOPIA CARNET DE SEGURO',
            'CERTIFICADO DE NO TENER DEUDAS',
            'CERTIFICADO DE APORTES',
            'CERTIFICADO DE SOLVENCIA',
        ];
        foreach($requirements as $requirement) {
            Requirement::firstOrCreate([
                'name' => $requirement,
            ], []);
        }
    }
}
