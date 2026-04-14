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

            // Adaptações por disciplinas do PEI
            ['name' => 'Visualizar Adaptação do PEI', 'slug' => 'pei-discipline.view'],
            ['name' => 'Criar Adaptação do PEI',      'slug' => 'pei-discipline.create'],
            ['name' => 'Editar Adaptação do PEI',     'slug' => 'pei-discipline.update'],
            ['name' => 'Excluir Adaptação do PEI',    'slug' => 'pei-discipline.delete'],

            // Documentos do Aluno
            ['name' => 'Visualizar Documentos', 'slug' => 'student-document.view'],
            ['name' => 'Criar Documento',      'slug' => 'student-document.create'],
            ['name' => 'Editar Documento',     'slug' => 'student-document.update'],
            ['name' => 'Excluir Documento',    'slug' => 'student-document.delete'],

            // Tecnologias Assistivas
            ['name' => 'Listar Tecnologias Assistivas',          'slug' => 'assistive-technology.index'],
            ['name' => 'Formulário Criar Tecnologia Assistiva',  'slug' => 'assistive-technology.create'],
            ['name' => 'Salvar Tecnologia Assistiva',            'slug' => 'assistive-technology.store'],
            ['name' => 'Visualizar Tecnologia Assistiva',        'slug' => 'assistive-technology.show'],
            ['name' => 'Formulário Editar Tecnologia Assistiva', 'slug' => 'assistive-technology.edit'],
            ['name' => 'Atualizar Tecnologia Assistiva',         'slug' => 'assistive-technology.update'],
            ['name' => 'Excluir Tecnologia Assistiva',           'slug' => 'assistive-technology.destroy'],
            ['name' => 'Gerar PDF de Tecnologia Assistiva',      'slug' => 'assistive-technology.pdf'],
            ['name' => 'Ver Logs de Tecnologia Assistiva',       'slug' => 'assistive-technology.logs'],
            ['name' => 'Ver Inspeção de Tecnologia Assistiva',   'slug' => 'assistive-technology.inspection.show'],

            // Materiais Pedagógicos Acessíveis
            ['name' => 'Listar Materiais Pedagógicos',          'slug' => 'material.index'],
            ['name' => 'Formulário Criar Material Pedagógico',  'slug' => 'material.create'],
            ['name' => 'Salvar Material Pedagógico',            'slug' => 'material.store'],
            ['name' => 'Visualizar Material Pedagógico',        'slug' => 'material.show'],
            ['name' => 'Formulário Editar Material Pedagógico', 'slug' => 'material.edit'],
            ['name' => 'Atualizar Material Pedagógico',         'slug' => 'material.update'],
            ['name' => 'Excluir Material Pedagógico',           'slug' => 'material.destroy'],
            ['name' => 'Gerar PDF de Material Pedagógico',      'slug' => 'material.pdf'],
            ['name' => 'Ver Logs de Material Pedagógico',       'slug' => 'material.logs'],
            ['name' => 'Ver Inspeção de Material Pedagógico',   'slug' => 'material.inspection.show'],

            // Barreiras
            ['name' => 'Listar Barreiras',          'slug' => 'barrier.index'],
            ['name' => 'Formulário Criar Barreira',  'slug' => 'barrier.create'],
            ['name' => 'Salvar Barreira',            'slug' => 'barrier.store'],
            ['name' => 'Visualizar Barreira',        'slug' => 'barrier.show'],
            ['name' => 'Formulário Editar Barreira', 'slug' => 'barrier.edit'],
            ['name' => 'Atualizar Barreira',         'slug' => 'barrier.update'],
            ['name' => 'Excluir Barreira',           'slug' => 'barrier.destroy'],
            ['name' => 'Gerar PDF de Barreira',      'slug' => 'barrier.pdf'],
            ['name' => 'Ver Inspeção de Barreira',   'slug' => 'barrier.inspection.show'],

            // Empréstimos
            ['name' => 'Listar Empréstimos',          'slug' => 'loan.index'],
            ['name' => 'Formulário Criar Empréstimo',  'slug' => 'loan.create'],
            ['name' => 'Salvar Empréstimo',            'slug' => 'loan.store'],
            ['name' => 'Visualizar Empréstimo',        'slug' => 'loan.show'],
            ['name' => 'Formulário Editar Empréstimo', 'slug' => 'loan.edit'],
            ['name' => 'Atualizar Empréstimo',         'slug' => 'loan.update'],
            ['name' => 'Registrar Devolução',          'slug' => 'loan.return'],
            ['name' => 'Excluir Empréstimo',           'slug' => 'loan.destroy'],
            ['name' => 'Gerar PDF de Empréstimo',      'slug' => 'loan.pdf'],

            // Agenda Institucional
            ['name' => 'Listar Eventos Institucionais',          'slug' => 'institutional-event.index'],
            ['name' => 'Formulário Criar Evento Institucional',  'slug' => 'institutional-event.create'],
            ['name' => 'Salvar Evento Institucional',            'slug' => 'institutional-event.store'],
            ['name' => 'Visualizar Evento Institucional',        'slug' => 'institutional-event.show'],
            ['name' => 'Formulário Editar Evento Institucional', 'slug' => 'institutional-event.edit'],
            ['name' => 'Atualizar Evento Institucional',         'slug' => 'institutional-event.update'],
            ['name' => 'Excluir Evento Institucional',           'slug' => 'institutional-event.destroy'],
            ['name' => 'Gerar PDF de Evento Institucional',      'slug' => 'institutional-event.pdf'],

            // Fila de Espera (Waitlist)
            ['name' => 'Listar Fila de Espera',           'slug' => 'waitlist.index'],
            ['name' => 'Formulário Criar Fila de Espera', 'slug' => 'waitlist.create'],
            ['name' => 'Salvar Fila de Espera',           'slug' => 'waitlist.store'],
            ['name' => 'Visualizar Fila de Espera',       'slug' => 'waitlist.show'],
            ['name' => 'Formulário Editar Fila de Espera','slug' => 'waitlist.edit'],
            ['name' => 'Atualizar Fila de Espera',        'slug' => 'waitlist.update'],
            ['name' => 'Excluir Fila de Espera',          'slug' => 'waitlist.destroy'],
            ['name' => 'Cancelar Fila de Espera',         'slug' => 'waitlist.cancel'],
            ['name' => 'Gerar PDF de Fila de Espera',     'slug' => 'waitlist.pdf'],

            // Categorias de Barreira
            ['name' => 'Listar Categorias de Barreira',          'slug' => 'barrier-category.index'],
            ['name' => 'Formulário Criar Categoria de Barreira',  'slug' => 'barrier-category.create'],
            ['name' => 'Salvar Categoria de Barreira',            'slug' => 'barrier-category.store'],
            ['name' => 'Visualizar Categoria de Barreira',        'slug' => 'barrier-category.show'],
            ['name' => 'Formulário Editar Categoria de Barreira', 'slug' => 'barrier-category.edit'],
            ['name' => 'Atualizar Categoria de Barreira',         'slug' => 'barrier-category.update'],
            ['name' => 'Excluir Categoria de Barreira',           'slug' => 'barrier-category.destroy'],

            // Instituições
            ['name' => 'Listar Instituições',          'slug' => 'institution.index'],
            ['name' => 'Formulário Criar Instituição',  'slug' => 'institution.create'],
            ['name' => 'Salvar Instituição',            'slug' => 'institution.store'],
            ['name' => 'Visualizar Instituição',        'slug' => 'institution.show'],
            ['name' => 'Formulário Editar Instituição', 'slug' => 'institution.edit'],
            ['name' => 'Atualizar Instituição',         'slug' => 'institution.update'],
            ['name' => 'Excluir Instituição',           'slug' => 'institution.destroy'],

            // Localizações
            ['name' => 'Listar Localizações',          'slug' => 'location.index'],
            ['name' => 'Formulário Criar Localização',  'slug' => 'location.create'],
            ['name' => 'Salvar Localização',            'slug' => 'location.store'],
            ['name' => 'Visualizar Localização',        'slug' => 'location.show'],
            ['name' => 'Formulário Editar Localização', 'slug' => 'location.edit'],
            ['name' => 'Atualizar Localização',         'slug' => 'location.update'],
            ['name' => 'Excluir Localização',           'slug' => 'location.destroy'],

            // Recursos de Acessibilidade
            ['name' => 'Listar Recursos de Acessibilidade',          'slug' => 'accessibility-feature.index'],
            ['name' => 'Formulário Criar Recurso de Acessibilidade',  'slug' => 'accessibility-feature.create'],
            ['name' => 'Salvar Recurso de Acessibilidade',            'slug' => 'accessibility-feature.store'],
            ['name' => 'Visualizar Recurso de Acessibilidade',        'slug' => 'accessibility-feature.show'],
            ['name' => 'Formulário Editar Recurso de Acessibilidade', 'slug' => 'accessibility-feature.edit'],
            ['name' => 'Atualizar Recurso de Acessibilidade',         'slug' => 'accessibility-feature.update'],
            ['name' => 'Excluir Recurso de Acessibilidade',           'slug' => 'accessibility-feature.destroy'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['slug' => $p['slug']], ['name' => $p['name']]);
        }

        // Atribuir todas as permissões ao Professor AEE
        $professorAee = Position::where('name', 'Professor AEE')->first();

        if ($professorAee) {
            $allPermissionIds = Permission::pluck('id')->toArray();

            if (method_exists($professorAee, 'permissions')) {
                $professorAee->permissions()->sync($allPermissionIds);
            } else {
                $pivotData = array_map(function ($id) use ($professorAee) {
                    return [
                        'position_id'   => $professorAee->id,
                        'permission_id' => $id,
                    ];
                }, $allPermissionIds);

                \Illuminate\Support\Facades\DB::table('position_permission')->insertOrIgnore($pivotData);
            }
        }
    }
}
