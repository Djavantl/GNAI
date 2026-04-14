<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use App\Models\SpecializedEducationalSupport\Pei;
use App\Models\SpecializedEducationalSupport\PeiDiscipline;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Semester;
use App\Models\SpecializedEducationalSupport\TeacherCourseDiscipline;
use App\Models\User;

class PeiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::with(['currentCourse.course.disciplines', 'currentContext'])->get();
        $semester = Semester::where('is_current', true)->first();
        $creator = User::whereNotNull('professional_id')->first();

        if (!$semester || !$creator) {
            $this->command->warn('Certifique-se de ter Semestre atual e um usuário criador antes de rodar esta Seeder.');
            return;
        }

        foreach ($students as $student) {
            $currentCourseRelation = $student->currentCourse;
            $currentContext = $student->currentContext;

            if (!$currentCourseRelation || !$currentContext) {
                continue;
            }

            $course = $currentCourseRelation->course;

            if (!$course) {
                continue;
            }

            // PEI principal
            $pei = Pei::create([
                'student_id'         => $student->id,
                'creator_id'         => $creator->id,
                'semester_id'        => $semester->id,
                'course_id'          => $course->id,
                'student_context_id' => $currentContext->id,
                'is_finished'        => true,
                'version'            => 1,
                'is_current'         => true,
            ]);

            // Pega apenas disciplinas que possuem professor vinculado neste curso
            $disciplineIdsWithTeachers = TeacherCourseDiscipline::where('course_id', $course->id)
                ->distinct()
                ->pluck('discipline_id');

            $availableDisciplines = $course->disciplines
                ->whereIn('id', $disciplineIdsWithTeachers)
                ->values();

            if ($availableDisciplines->isEmpty()) {
                continue;
            }

            // Sorteia no máximo 3 disciplinas válidas
            $disciplines = $availableDisciplines->random(min(3, $availableDisciplines->count()));

            foreach ($disciplines as $discipline) {
                // Professores que realmente ministram esta disciplina neste curso
                $teacherIds = TeacherCourseDiscipline::where('course_id', $course->id)
                    ->where('discipline_id', $discipline->id)
                    ->pluck('teacher_id')
                    ->unique()
                    ->values();

                if ($teacherIds->isEmpty()) {
                    continue;
                }

                $teacherId = $teacherIds->random();

                PeiDiscipline::create([
                    'pei_id'        => $pei->id,
                    'teacher_id'    => $teacherId,
                    'creator_id'    => $creator->id,
                    'discipline_id' => $discipline->id,

                    'specific_objectives' => "Adaptar os objetivos da disciplina de {$discipline->name} para focar em competências essenciais, garantindo que o aluno compreenda os fundamentos básicos de forma prática.",

                    'content_programmatic' => "Seleção de tópicos prioritários: Introdução ao tema, aplicações práticas no cotidiano e exercícios de fixação com suporte visual.",

                    'methodologies' => "Utilização de metodologias ativas: \n" .
                        "- Tempo estendido para realização de tarefas;\n" .
                        "- Uso de materiais concretos e softwares de apoio;\n" .
                        "- Mediação constante do professor e monitoria entre pares.",

                    'evaluations' => "Avaliação processual e contínua através de:\n" .
                        "- Trabalhos práticos em grupo;\n" .
                        "- Provas adaptadas com enunciados curtos e claros;\n" .
                        "- Substituição de provas escritas por apresentações orais, se necessário.",

                    'opinion' => "O aluno demonstra interesse, mas necessita de suporte para organizar o pensamento lógico na disciplina de {$discipline->name}. Recomenda-se feedback constante.",
                ]);
            }
        }
    }
}