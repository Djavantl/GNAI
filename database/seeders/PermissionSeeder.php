<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\SpecializedEducationalSupport\Position;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // --- ADMINISTRAÇÃO / CADASTROS ---
    
            // Pessoas
            ['name' => 'Visualizar Pessoas', 'slug' => 'people.view'],
            ['name' => 'Criar Pessoa',      'slug' => 'people.create'],
            ['name' => 'Editar Pessoa',     'slug' => 'people.update'],
            ['name' => 'Excluir Pessoa',    'slug' => 'people.delete'],

            // Deficiências
            ['name' => 'Visualizar Deficiências', 'slug' => 'deficiency.view'],
            ['name' => 'Criar Deficiência',      'slug' => 'deficiency.create'],
            ['name' => 'Editar Deficiência',     'slug' => 'deficiency.update'],
            ['name' => 'Excluir Deficiência',    'slug' => 'deficiency.delete'],

            // Cargos (Positions)
            ['name' => 'Visualizar Cargos', 'slug' => 'position.view'],
            ['name' => 'Criar Cargo',      'slug' => 'position.create'],
            ['name' => 'Editar Cargo',     'slug' => 'position.update'],
            ['name' => 'Excluir Cargo',    'slug' => 'position.delete'],

            // Semestres
            ['name' => 'Visualizar Semestres', 'slug' => 'semester.view'],
            ['name' => 'Criar Semestre',      'slug' => 'semester.create'],
            ['name' => 'Editar Semestre',     'slug' => 'semester.update'],
            ['name' => 'Excluir Semestre',    'slug' => 'semester.delete'],

            // Cursos
            ['name' => 'Visualizar Cursos', 'slug' => 'course.view'],
            ['name' => 'Criar Curso',      'slug' => 'course.create'],
            ['name' => 'Editar Curso',     'slug' => 'course.update'],
            ['name' => 'Excluir Curso',    'slug' => 'course.delete'],

            // Disciplinas
            ['name' => 'Visualizar Disciplinas', 'slug' => 'discipline.view'],
            ['name' => 'Criar Disciplina',      'slug' => 'discipline.create'],
            ['name' => 'Editar Disciplina',     'slug' => 'discipline.update'],
            ['name' => 'Excluir Disciplina',    'slug' => 'discipline.delete'],

            // --- GESTÃO ESCOLAR / ATENDIMENTO ---

            // Alunos
            ['name' => 'Visualizar Alunos', 'slug' => 'student.view'],
            ['name' => 'Criar Aluno',      'slug' => 'student.create'],
            ['name' => 'Editar Aluno',     'slug' => 'student.update'],
            ['name' => 'Excluir Aluno',    'slug' => 'student.delete'],

            // Responsáveis (Guardians)
            ['name' => 'Visualizar Responsáveis', 'slug' => 'guardian.view'],
            ['name' => 'Criar Responsável',      'slug' => 'guardian.create'],
            ['name' => 'Editar Responsável',     'slug' => 'guardian.update'],
            ['name' => 'Excluir Responsável',    'slug' => 'guardian.delete'],

            // Profissionais
            ['name' => 'Visualizar Profissionais', 'slug' => 'professional.view'],
            ['name' => 'Criar Profissional',      'slug' => 'professional.create'],
            ['name' => 'Editar Profissional',     'slug' => 'professional.update'],
            ['name' => 'Excluir Profissional',    'slug' => 'professional.delete'],

            // Professores
            ['name' => 'Visualizar Professores', 'slug' => 'teacher.view'],
            ['name' => 'Criar Professor',      'slug' => 'teacher.create'],
            ['name' => 'Editar Professor',     'slug' => 'teacher.update'],
            ['name' => 'Excluir Professor',    'slug' => 'teacher.delete'],

            // Contexto do Aluno (Prontuário/Versões)
            ['name' => 'Visualizar Contexto do Aluno', 'slug' => 'student-context.view'],
            ['name' => 'Criar Contexto do Aluno',      'slug' => 'student-context.create'],
            ['name' => 'Editar Contexto do Aluno',     'slug' => 'student-context.update'],
            ['name' => 'Excluir Contexto do Aluno',    'slug' => 'student-context.delete'],

            // Deficiências do Aluno (Vínculo)
            ['name' => 'Visualizar Deficiências do Aluno', 'slug' => 'student-deficiency.view'],
            ['name' => 'Criar Deficiência do Aluno',      'slug' => 'student-deficiency.create'],
            ['name' => 'Editar Deficiência do Aluno',     'slug' => 'student-deficiency.update'],
            ['name' => 'Excluir Deficiência do Aluno',    'slug' => 'student-deficiency.delete'],

            // Sessões/Atendimentos
            ['name' => 'Visualizar Sessões', 'slug' => 'session.view'],
            ['name' => 'Criar Sessão',      'slug' => 'session.create'],
            ['name' => 'Editar Sessão',     'slug' => 'session.update'],
            ['name' => 'Excluir Sessão',    'slug' => 'session.delete'],

            // Registros de Sessão (Evolução)
            ['name' => 'Visualizar Registros de Sessão', 'slug' => 'session-record.view'],
            ['name' => 'Criar Registro de Sessão',      'slug' => 'session-record.create'],
            ['name' => 'Editar Registro de Sessão',     'slug' => 'session-record.update'],
            ['name' => 'Excluir Registro de Sessão',    'slug' => 'session-record.delete'],

            // Histórico/Cursos do Aluno
            ['name' => 'Visualizar Cursos do Aluno', 'slug' => 'student-course.view'],
            ['name' => 'Criar Curso do Aluno',      'slug' => 'student-course.create'],
            ['name' => 'Editar Curso do Aluno',     'slug' => 'student-course.update'],
            ['name' => 'Excluir Curso do Aluno',    'slug' => 'student-course.delete'],

            // Pendências
            ['name' => 'Visualizar Pendências', 'slug' => 'pendency.view'],
            ['name' => 'Criar Pendência',      'slug' => 'pendency.create'],
            ['name' => 'Editar Pendência',     'slug' => 'pendency.update'],
            ['name' => 'Excluir Pendência',    'slug' => 'pendency.delete'],

            // PEI (Plano Educacional Individualizado)
            ['name' => 'Visualizar PEI', 'slug' => 'pei.view'],
            ['name' => 'Criar PEI',      'slug' => 'pei.create'],
            ['name' => 'Editar PEI',     'slug' => 'pei.update'],
            ['name' => 'Excluir PEI',    'slug' => 'pei.delete'],

            // Avaliações do PEI
            ['name' => 'Visualizar Avaliação PEI', 'slug' => 'pei-evaluation.view'],
            ['name' => 'Criar Avaliação PEI',      'slug' => 'pei-evaluation.create'],
            ['name' => 'Editar Avaliação PEI',     'slug' => 'pei-evaluation.update'],
            ['name' => 'Excluir Avaliação PEI',    'slug' => 'pei-evaluation.delete'],

            // Documentos do Aluno
            ['name' => 'Visualizar Documentos', 'slug' => 'student-document.view'],
            ['name' => 'Criar Documento',      'slug' => 'student-document.create'],
            ['name' => 'Editar Documento',     'slug' => 'student-document.update'],
            ['name' => 'Excluir Documento',    'slug' => 'student-document.delete'],

            // --- RADAAAAR ---

            // Radar Inclusivo – Tecnologias Assistivas

            ['name' => 'Visualizar TAs (lista)', 'slug' => 'assistive-technology.index'],
            ['name' => 'Criar TA (form)', 'slug' => 'assistive-technology.create'],
            ['name' => 'Salvar TA', 'slug' => 'assistive-technology.store'],
            ['name' => 'Visualizar TA (show)', 'slug' => 'assistive-technology.show'],
            ['name' => 'Editar TA (form)', 'slug' => 'assistive-technology.edit'],
            ['name' => 'Atualizar TA', 'slug' => 'assistive-technology.update'],
            ['name' => 'Ativar/Desativar TA', 'slug' => 'assistive-technology.toggle'],
            ['name' => 'Excluir TA', 'slug' => 'assistive-technology.destroy'],
            ['name' => 'Gerar PDF da TA', 'slug' => 'assistive-technology.pdf'],

            // Radar Inclusivo – Materiais Pedagógicos Acessíveis

            ['name' => 'Visualizar Materiais (lista)', 'slug' => 'material.index'],
            ['name' => 'Criar Material (form)', 'slug' => 'material.create'],
            ['name' => 'Salvar Material', 'slug' => 'material.store'],
            ['name' => 'Visualizar Material (show)', 'slug' => 'material.show'],
            ['name' => 'Editar Material (form)', 'slug' => 'material.edit'],
            ['name' => 'Atualizar Material', 'slug' => 'material.update'],
            ['name' => 'Ativar/Desativar Material', 'slug' => 'material.toggle'],
            ['name' => 'Excluir Material', 'slug' => 'material.destroy'],
            ['name' => 'Gerar PDF do Material', 'slug' => 'material.pdf'],

            // Radar Inclusivo – Barreiras (operacional)

            ['name' => 'Visualizar Barreiras (lista)', 'slug' => 'barrier.index'],
            ['name' => 'Criar Barreira (form)', 'slug' => 'barrier.create'],
            ['name' => 'Salvar Barreira', 'slug' => 'barrier.store'],
            ['name' => 'Visualizar Barreira (detalhes)', 'slug' => 'barrier.show'],
            ['name' => 'Editar Barreira (form)', 'slug' => 'barrier.edit'],
            ['name' => 'Atualizar Barreira', 'slug' => 'barrier.update'],
            ['name' => 'Ativar/Desativar Barreira', 'slug' => 'barrier.toggle'],
            ['name' => 'Excluir Barreira', 'slug' => 'barrier.destroy'],

            // Radar Inclusivo – Empréstimos

            ['name' => 'Visualizar Empréstimos (lista)', 'slug' => 'loan.index'],
            ['name' => 'Criar Empréstimo (form)', 'slug' => 'loan.create'],
            ['name' => 'Salvar Empréstimo', 'slug' => 'loan.store'],
            ['name' => 'Visualizar Empréstimo (show)', 'slug' => 'loan.show'],
            ['name' => 'Editar Empréstimo (form)', 'slug' => 'loan.edit'],
            ['name' => 'Atualizar Empréstimo', 'slug' => 'loan.update'],
            ['name' => 'Registrar Devolução', 'slug' => 'loan.return'],
            ['name' => 'Excluir Empréstimo', 'slug' => 'loan.destroy'],

            // Radar Inclusivo – Relatórios (se houver)

            ['name' => 'Acessar Relatórios', 'slug' => 'report.index'],
            ['name' => 'Configurar Relatório', 'slug' => 'report.configure'],
            ['name' => 'Exportar Relatório (PDF/Excel)', 'slug' => 'report.export'],

            // Permissões para cadastros ADMIN (apenas middleware admin)

            ['name' => 'Gerenciar Tipos de Recurso', 'slug' => 'resource-type.manage'],
            ['name' => 'Gerenciar Atributos de Tipo', 'slug' => 'type-attribute.manage'],
            ['name' => 'Gerenciar Atribuições Tipo-Atributo', 'slug' => 'type-attribute-assignment.manage'],
            ['name' => 'Gerenciar Categorias de Barreira', 'slug' => 'barrier-category.manage'],
            ['name' => 'Gerenciar Instituições', 'slug' => 'institution.manage'],
            ['name' => 'Gerenciar Localizações', 'slug' => 'location.manage'],
            ['name' => 'Gerenciar Recursos de Acessibilidade', 'slug' => 'accessibility-feature.manage'],
            ['name' => 'Gerenciar Status de Recurso', 'slug' => 'resource-status.manage'],

            // Relatórios
            ['name' => 'Acessar Relatórios (lista)', 'slug' => 'report.index'],
            ['name' => 'Configurar Filtros do Relatório', 'slug' => 'report.configure'],
            ['name' => 'Gerar Relatório', 'slug' => 'report.generate'],          // <-- nova
            ['name' => 'Exportar Relatório (PDF/Excel/HTML)', 'slug' => 'report.export'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['slug' => $p['slug']], ['name' => $p['name']]);
        }

        // 2. Atribuir todas as permissões ao Professor AEE
        $professorAee = Position::where('name', 'Professor AEE')->first();

        if ($professorAee) {
            // Pegamos os IDs de todas as permissões criadas acima
            $allPermissionIds = Permission::pluck('id')->toArray();
            
            // Sincroniza sem remover as existentes (ou use sync para resetar)
            // Se o seu model Position tiver a relação permissions():
            if (method_exists($professorAee, 'permissions')) {
                $professorAee->permissions()->sync($allPermissionIds);
            } else {
                // Caso você não tenha o relacionamento no model, inserimos via DB na tabela pivô
                $pivotData = array_map(function($id) use ($professorAee) {
                    return [
                        'position_id' => $professorAee->id,
                        'permission_id' => $id,
                    ];
                }, $allPermissionIds);

                \Illuminate\Support\Facades\DB::table('position_permission')->insertOrIgnore($pivotData);
            }
        }
    }
}
