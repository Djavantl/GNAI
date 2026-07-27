<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Teacher;
use App\Models\SpecializedEducationalSupport\Course;
use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\TeacherCourseDiscipline;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachersData = [
            ['name' => 'João Neves', 'gender' => 'male', 'course' => 'Técnico em Informática'],
            ['name' => 'Maria Da Conseição', 'gender' => 'female', 'course' => 'Técnico em Administração'],
            ['name' => 'Roberto Brito', 'gender' => 'male', 'course' => 'Técnico em Mecânica'],
            ['name' => 'Marcio Lima', 'gender' => 'male', 'course' => 'Técnico em Informática'],
        ];

        foreach ($teachersData as $index => $data) {
            DB::transaction(function () use ($data, $index) {
                $cpf = $this->generateValidCpf($index + 1);

                $person = Person::firstOrCreate(
                    ['document' => $cpf],
                    [
                        'name' => $data['name'],
                        'birth_date' => now()->subYears(rand(28, 55))->format('Y-m-d'),
                        'gender' => $data['gender'],
                        'email' => strtolower(str_replace(' ', '.', $data['name'])).'@escola.com',
                    ]
                );

                $teacher = Teacher::firstOrCreate(
                    ['person_id' => $person->id],
                    [
                        'registration' => 'DOC'.str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    ]
                );

                $course = Course::where('name', $data['course'])->first();

                if ($course) {
                    $teacher->courses()->syncWithoutDetaching([$course->id]);

                    $disciplineIds = $course->disciplines()
                        ->orderBy('name')
                        ->limit(7)
                        ->pluck('disciplines.id')
                        ->toArray();

                    foreach ($disciplineIds as $disciplineId) {
                        TeacherCourseDiscipline::firstOrCreate([
                            'teacher_id' => $teacher->id,
                            'course_id' => $course->id,
                            'discipline_id' => $disciplineId,
                        ]);
                    }
                }

                User::firstOrCreate(
                    ['email' => $person->email],
                    [
                        'name' => $person->name,
                        'password' => Hash::make('napne2026'),
                        'role' => 'teacher',
                        'teacher_id' => $teacher->id,
                    ]
                );
            });
        }
    }

    private function generateValidCpf(int $seed = 1): string
    {
        $base = str_pad((string) (100000000 + ($seed * 12345) % 900000000), 9, '0', STR_PAD_LEFT);

        $digits = array_map('intval', str_split($base));

        $sum1 = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum1 += $digits[$i] * (10 - $i);
        }
        $remainder1 = $sum1 % 11;
        $digit1 = ($remainder1 < 2) ? 0 : 11 - $remainder1;

        $sum2 = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum2 += $digits[$i] * (11 - $i);
        }
        $sum2 += $digit1 * 2;
        $remainder2 = $sum2 % 11;
        $digit2 = ($remainder2 < 2) ? 0 : 11 - $remainder2;

        return $base.$digit1.$digit2;
    }
}
