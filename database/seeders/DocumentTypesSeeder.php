<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $documents = [
            [
                'name' => 'CARNET DE IDENTIDAD',
                'code' => 'CI',
            ], [
                'name' => 'CARNET MILITAR',
                'code' => 'CM',
            ], [
                'name' => 'CARNET DE SOCIO',
                'code' => 'CS',
            ],
        ];

        foreach($documents as $document) {
            DocumentType::firstOrCreate([
                'code' => $document['code'],
            ], [
                'name' => $document['name'],
            ]);
        }
    }
}
