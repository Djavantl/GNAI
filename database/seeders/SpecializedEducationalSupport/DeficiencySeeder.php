<?php

namespace Database\Seeders\SpecializedEducationalSupport;

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
                'name' => 'Deficiência Visual',
                'cid_code' => 'H54',
                'description' => 'Deficiência visual, incluindo baixa visão e cegueira parcial ou total.'
            ],
            [
                'name' => 'Deficiência Auditiva',
                'cid_code' => 'H90',
                'description' => 'Perda auditiva parcial ou total, podendo ser unilateral ou bilateral.'
            ],
            [
                'name' => 'Deficiência Física',
                'cid_code' => 'G80',
                'description' => 'Comprometimentos motores que afetam mobilidade, coordenação ou força física.'
            ],
            [
                'name' => 'Deficiência Intelectual',
                'cid_code' => 'F70',
                'description' => 'Limitações significativas no funcionamento intelectual e no comportamento adaptativo.'
            ],
            [
                'name' => 'Deficiência Psicossocial',
                'cid_code' => 'F32',
                'description' => 'Condições que afetam o comportamento, emoção e interação social do indivíduo.'
            ],
            [
                'name' => 'Surdocegueira',
                'cid_code' => 'H54.0',
                'description' => 'Deficiência singular que apresenta perda auditiva e visual concomitante.'
            ],

            // --- NEURODIVERGÊNCIAS E TRANSTORNOS (Muito comuns no NAPNE) ---
            [
                'name' => 'Transtorno do Espectro Autista (TEA)',
                'cid_code' => 'F84',
                'description' => 'Déficits na comunicação social, interação e padrões restritos de comportamento.'
            ],
            [
                'name' => 'TDAH',
                'cid_code' => 'F90',
                'description' => 'Transtorno do Déficit de Atenção com Hiperatividade, exigindo estratégias de foco e tempo.'
            ],
            [
                'name' => 'Altas Habilidades / Superdotação',
                'cid_code' => 'Z73.3',
                'description' => 'Potencial elevado em uma ou mais áreas do conhecimento, exigindo enriquecimento curricular.'
            ],
            [
                'name' => 'Transtorno Global do Desenvolvimento (TGD)',
                'cid_code' => 'F84.9',
                'description' => 'Distúrbios nas interações sociais recíprocas e formas de comunicação.'
            ],
        ]);
    }
}
