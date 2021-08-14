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
                'name' => 'CÉDULA DE IDENTIDAD',
                'code' => 'CI',
            ], [
                'name' => 'CÉDULA MILITAR',
                'code' => 'CM',
            ], [
                'name' => 'CÉDULA ADMINISTRATIVA',
                'code' => 'CA',
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
