<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Students
            ['name' => 'Visualizar Alunos (lista)', 'slug' => 'student.index'],
            ['name' => 'Visualizar Aluno (show)', 'slug' => 'student.show'],
            ['name' => 'Criar Aluno (form)', 'slug' => 'student.create'],
            ['name' => 'Salvar Aluno', 'slug' => 'student.store'],
            ['name' => 'Editar Aluno (form)', 'slug' => 'student.edit'],
            ['name' => 'Atualizar Aluno', 'slug' => 'student.update'],
            ['name' => 'Excluir Aluno', 'slug' => 'student.destroy'],

            // Guardians
            ['name' => 'Visualizar Responsáveis (lista)', 'slug' => 'guardian.index'],
            ['name' => 'Visualizar Responsável (show)', 'slug' => 'guardian.show'],
            ['name' => 'Criar Responsável (form)', 'slug' => 'guardian.create'],
            ['name' => 'Salvar Responsável', 'slug' => 'guardian.store'],
            ['name' => 'Editar Responsável (form)', 'slug' => 'guardian.edit'],
            ['name' => 'Atualizar Responsável', 'slug' => 'guardian.update'],
            ['name' => 'Excluir Responsável', 'slug' => 'guardian.destroy'],

            // Professionals
            ['name' => 'Visualizar Profissionais (lista)', 'slug' => 'professional.index'],
            ['name' => 'Visualizar Profissional (show)', 'slug' => 'professional.show'],
            ['name' => 'Criar Profissional (form)', 'slug' => 'professional.create'],
            ['name' => 'Salvar Profissional', 'slug' => 'professional.store'],
            ['name' => 'Editar Profissional (form)', 'slug' => 'professional.edit'],
            ['name' => 'Atualizar Profissional', 'slug' => 'professional.update'],
            ['name' => 'Excluir Profissional', 'slug' => 'professional.destroy'],

            // Student Context (contexto do aluno)
            ['name' => 'Visualizar Contextos do Aluno (lista)', 'slug' => 'student-context.index'],
            ['name' => 'Visualizar Contexto do Aluno (show)', 'slug' => 'student-context.show'],
            ['name' => 'Visualizar Contexto Atual do Aluno', 'slug' => 'student-context.show-current'],
            ['name' => 'Definir Contexto Atual do Aluno', 'slug' => 'student-context.set-current'],
            ['name' => 'Criar Contexto do Aluno (form)', 'slug' => 'student-context.create'],
            ['name' => 'Salvar Contexto do Aluno', 'slug' => 'student-context.store'],
            ['name' => 'Editar Contexto do Aluno (form)', 'slug' => 'student-context.edit'],
            ['name' => 'Atualizar Contexto do Aluno', 'slug' => 'student-context.update'],
            ['name' => 'Excluir Contexto do Aluno', 'slug' => 'student-context.destroy'],
            ['name' => 'Gerar PDF do Contexto do Aluno', 'slug' => 'student-context.pdf'],

            // Student Deficiencies (deficiências do aluno)
            ['name' => 'Visualizar Deficiências do Aluno (lista)', 'slug' => 'student-deficiency.index'],
            ['name' => 'Visualizar Deficiência do Aluno (show)', 'slug' => 'student-deficiency.show'],
            ['name' => 'Criar Deficiência (form)', 'slug' => 'student-deficiency.create'],
            ['name' => 'Salvar Deficiência', 'slug' => 'student-deficiency.store'],
            ['name' => 'Editar Deficiência (form)', 'slug' => 'student-deficiency.edit'],
            ['name' => 'Atualizar Deficiência', 'slug' => 'student-deficiency.update'],
            ['name' => 'Excluir Deficiência', 'slug' => 'student-deficiency.destroy'],

            // Sessions (sessões de atendimento)
            ['name' => 'Visualizar Sessões (lista)', 'slug' => 'session.index'],
            ['name' => 'Criar Sessão (form)', 'slug' => 'session.create'],
            ['name' => 'Salvar Sessão', 'slug' => 'session.store'],
            ['name' => 'Visualizar Sessão (show)', 'slug' => 'session.show'],
            ['name' => 'Editar Sessão (form)', 'slug' => 'session.edit'],
            ['name' => 'Atualizar Sessão', 'slug' => 'session.update'],
            ['name' => 'Excluir Sessão', 'slug' => 'session.destroy'],
            ['name' => 'Restaurar Sessão (withTrashed)', 'slug' => 'session.restore'],
            ['name' => 'Excluir Definitivamente Sessão (force delete)', 'slug' => 'session.force-delete'],

            // Session Records (registros de sessão)
            ['name' => 'Visualizar Registros de Sessão (lista)', 'slug' => 'session-record.index'],
            ['name' => 'Criar Registro de Sessão (form)', 'slug' => 'session-record.create'],
            ['name' => 'Salvar Registro de Sessão', 'slug' => 'session-record.store'],
            ['name' => 'Visualizar Registro de Sessão (show)', 'slug' => 'session-record.show'],
            ['name' => 'Editar Registro de Sessão (form)', 'slug' => 'session-record.edit'],
            ['name' => 'Atualizar Registro de Sessão', 'slug' => 'session-record.update'],
            ['name' => 'Excluir Registro de Sessão', 'slug' => 'session-record.destroy'],
            ['name' => 'Restaurar Registro de Sessão (withTrashed)', 'slug' => 'session-record.restore'],
            ['name' => 'Excluir Definitivamente Registro de Sessão (force delete)', 'slug' => 'session-record.force-delete'],
            ['name' => 'Gerar PDF do Registro de Sessão', 'slug' => 'session-record.pdf'],

            // Student Courses (matrícula / histórico)
            ['name' => 'Criar Matrícula (form)', 'slug' => 'student-course.create'],
            ['name' => 'Salvar Matrícula', 'slug' => 'student-course.store'],
            ['name' => 'Visualizar Histórico do Aluno', 'slug' => 'student-course.history'],
            ['name' => 'Editar Matrícula (form)', 'slug' => 'student-course.edit'],
            ['name' => 'Atualizar Matrícula', 'slug' => 'student-course.update'],
            ['name' => 'Excluir Matrícula', 'slug' => 'student-course.destroy'],

            // Pendencies (pendências)
            ['name' => 'Visualizar Pendências (lista)', 'slug' => 'pendency.index'],
            ['name' => 'Visualizar Pendência (show)', 'slug' => 'pendency.show'],
            ['name' => 'Criar Pendência (form)', 'slug' => 'pendency.create'],
            ['name' => 'Salvar Pendência', 'slug' => 'pendency.store'],
            ['name' => 'Editar Pendência (form)', 'slug' => 'pendency.edit'],
            ['name' => 'Atualizar Pendência', 'slug' => 'pendency.update'],
            ['name' => 'Excluir Pendência', 'slug' => 'pendency.destroy'],
            ['name' => 'Minhas Pendências (apenas do usuário)', 'slug' => 'pendency.my'],
            ['name' => 'Marcar Pendência como Concluída', 'slug' => 'pendency.complete'],

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
    }
}
