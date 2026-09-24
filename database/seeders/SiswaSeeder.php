<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Siswa;
use Faker\Factory as Faker;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $kelas = [
            'X-RPL-1', 'X-RPL-2', 'X-RPL-3', 'X-RPL-4', 'X-RPL-5',
            'X-TOI-1', 'X-TOI-2', 'X-TOI-3', 'X-TOI-4', 'X-TOI-5',
            'X-TKJ-1', 'X-TKJ-2', 'X-TKJ-3', 'X-TKJ-4', 'X-TKJ-5',
            'X-DKV-1', 'X-DKV-2', 'X-DKV-3', 'X-DKV-4', 'X-DKV-5',
            'X-LPB-1', 'X-LPB-2', 'X-LPB-3', 'X-LPB-4', 'X-LPB-5',
            'XI-RPL-1', 'XI-RPL-2', 'XI-TOI-1', 'XI-TKJ-1', 'XI-TKJ-2',
            'XI-DKV-1', 'XI-DKV-2', 'XI-DKV-3', 'XI-LPB-1', 'XI-LPB-2',
            'XII-RPL-1', 'XII-RPL-2', 'XII-TKJ-1', 'XII-TKJ-2', 'XII-TKJ-3',
            'XII-DKV-1', 'XII-DKV-2', 'XII-DKV-3', 'XII-DKV-4', 'XII-LPB-1', 'XII-LPB-2'
        ];

        $data = [];

        // Buat tepat 1 siswa untuk setiap kelas
        foreach ($kelas as $k) {
            $data[] = [
                'nisn' => $faker->unique()->numerify('##########'),
                'nama' => $faker->name,
                'kelas' => $k,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert per batch agar aman (chunk)
        $chunks = array_chunk($data, 50);
        foreach ($chunks as $chunk) {
            Siswa::insert($chunk);
        }
    }
}
