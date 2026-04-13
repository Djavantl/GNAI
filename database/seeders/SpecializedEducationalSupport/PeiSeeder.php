<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use App\Models\SpecializedEducationalSupport\Pei;
use App\Models\SpecializedEducationalSupport\PeiDiscipline;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Semester;
use App\Models\SpecializedEducationalSupport\Teacher;
use App\Models\User;
use Illuminate\Support\Arr;

class PeiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::with(['currentCourse.course.disciplines', 'currentContext'])->get();
        $semester = Semester::where('is_current', true)->first();
        $creator = User::first(); // Assumindo que existe um usuário administrador
        $teachers = Teacher::all();

        if (!$semester || !$creator || $teachers->isEmpty()) {
            $this->command->warn('Certifique-se de ter Semestres, Usuários e Professores antes de rodar esta Seeder.');
            return;
        }

        foreach ($students as $student) {
            $currentCourseRelation = $student->currentCourse;
            $currentContext = $student->currentContext;

            if (!$currentCourseRelation || !$currentContext) {
                continue;
            }

            $course = $currentCourseRelation->course;

            // 1. Criar o cabeçalho do PEI
            $pei = Pei::create([
                'student_id'         => $student->id,
                'creator_id'         => $creator->id,
                'semester_id'        => $semester->id,
                'course_id'          => $course->id,
                'student_context_id' => $currentContext->id,
                'is_finished'        => true, // Marcado como finalizado para apresentação
                'version'            => 1,
                'is_current'         => true,
            ]);

            // 2. Criar adaptações para as disciplinas do curso do aluno
            // Vamos pegar 3 disciplinas aleatórias do curso para não ficar repetitivo
            $disciplines = $course->disciplines->random(min(3, $course->disciplines->count()));

            foreach ($disciplines as $discipline) {
                PeiDiscipline::create([
                    'pei_id'        => $pei->id,
                    'teacher_id'    => $teachers->random()->id,
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