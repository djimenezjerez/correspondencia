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
                'name' => 'OFICIO',
                'code' => 'OF',
                'area' => null,
                'requirements' => [],
            ], [
                'name' => 'SOLICITUD DE PRÉSTAMO',
                'code' => 'SDP',
                'area' => 'SECRETARÍA DE HACIENDA',
                'requirements' => [
                    'BOLETA DE PAGO DE LOS 3 ÚLTIMOS MESES',
                    'FOTOCOPIA DE CÉDULA DE IDENTIDAD',
                    'CERTIFICADO DE NACIMIENTO',
                ],
            ],
        ];

        foreach($procedure_types as $procedure_type) {
            if ($procedure_type['area']) {
                $area = Area::where('name', $procedure_type['area'])->firstOrFail();
                $area_id = $area->id;
            } else {
                $area_id = null;
            }

            $new_procedure_type = ProcedureType::firstOrCreate([
                'code' => $procedure_type['code'],
            ], [
                'name' => $procedure_type['name'],
                'area_id' => $area_id,
            ]);

            $requirements = [];
            foreach ($procedure_type['requirements'] as $requirement) {
                $new_equirement = Requirement::firstOrCreate([
                    'name' => $requirement
                ], []);
                $requirements[] = $new_equirement->id;
            }
            $new_procedure_type->requirements()->sync($requirements);
        }
    }
}
