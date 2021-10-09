<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\ProcedureType;
use App\Models\Requirement;
use Illuminate\Database\Seeder;

class ProcedureTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $procedure_types = [
            [
                'name' => 'Tramite General',
                'code' => 'TG',
                'requirements' => [],
            ], [
                'name' => 'Préstamo de Emergencia',
                'code' => 'PE',
                'requirements' => [
                    'FOTOCOPIA BOLETA DE PAGO (MES ANTERIOR)',
                    'EXTRACTO BANCARIO',
                    'FOTOCOPIA CARNET DE IDENTIDAD',
                    'FOTOCOPIA CARNET MILITAR',
                    'FOTOCOPIA CARNET DE SEGURO',
                ],
            ], [
                'name' => 'Préstamo Regular',
                'code' => 'PR',
                'requirements' => [
                    'FOTOCOPIA BOLETA DE PAGO (MES ANTERIOR)',
                    'EXTRACTO BANCARIO',
                    'FOTOCOPIA CARNET DE IDENTIDAD',
                    'FOTOCOPIA CARNET MILITAR',
                    'FOTOCOPIA CARNET DE SEGURO',
                ],
            ], [
                'name' => 'Préstamo Iniciación',
                'code' => 'PI',
                'requirements' => [
                    'FOTOCOPIA BOLETA DE PAGO (MES ANTERIOR)',
                    'EXTRACTO BANCARIO',
                    'FOTOCOPIA CARNET DE IDENTIDAD',
                    'FOTOCOPIA CARNET MILITAR',
                    'FOTOCOPIA CARNET DE SEGURO',
                    'CERTIFICADO DE NO TENER DEUDAS',
                    'CERTIFICADO DE APORTES',
                    'CERTIFICADO DE SOLVENCIA',
                ],
            ],
        ];

        foreach($procedure_types as $procedure_type) {
            $new_procedure_type = ProcedureType::firstOrCreate([
                'code' => $procedure_type['code'],
            ], [
                'name' => $procedure_type['name'],
            ]);

            $requirements = [];
            foreach ($procedure_type['requirements'] as $requirement) {
                $new_equirement = Requirement::firstOrCreate([
                    'name' => $requirement,
                ], []);
                $requirements[] = $new_equirement->id;
            }
            $new_procedure_type->requirements()->sync($requirements);
        }
    }
}
