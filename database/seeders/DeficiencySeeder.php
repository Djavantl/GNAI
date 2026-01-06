<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DeficiencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('deficiencies')->insert([
            [
                'name' => 'Visual',
                'cid_code' => 'H54',
                'description' => 'Deficiência visual, incluindo baixa visão e cegueira parcial ou total.'
            ],
            [
                'name' => 'Auditiva',
                'cid_code' => 'H90',
                'description' => 'Perda auditiva parcial ou total, podendo ser unilateral ou bilateral.'
            ],
            [
                'name' => 'Física',
                'cid_code' => 'G80',
                'description' => 'Comprometimentos motores que afetam mobilidade, coordenação ou força física.'
            ],
            [
                'name' => 'Intelectual',
                'cid_code' => 'F70',
                'description' => 'Limitações significativas no funcionamento intelectual e no comportamento adaptativo.'
            ],
            [
                'name' => 'Psicossocial',
                'cid_code' => 'F32',
                'description' => 'Condições que afetam o comportamento, emoção e interação social do indivíduo.'
            ],
        ]);
    }
}
