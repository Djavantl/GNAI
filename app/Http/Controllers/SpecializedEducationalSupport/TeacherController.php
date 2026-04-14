<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecializedEducationalSupport\TeacherRequest;
use App\Models\SpecializedEducationalSupport\Discipline;
use App\Models\SpecializedEducationalSupport\Course;
use App\Models\SpecializedEducationalSupport\Teacher;
use App\Models\Permission;
use App\Services\SpecializedEducationalSupport\TeacherService;
use Illuminate\Http\Request;
use Throwable;

class TeacherController extends Controller
{
    protected TeacherService $service;

    public function __construct(TeacherService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $teachers = $this->service->index($request->all());
            $disciplines = Discipline::orderBy('name')->get(['id', 'name']);

            if ($request->ajax()) {
                return view(
                    'pages.specialized-educational-support.teachers.partials.table',
                    compact('teachers', 'disciplines')
                )->render();
            }

            return view(
                'pages.specialized-educational-support.teachers.index',
                compact('teachers', 'disciplines')
            );

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao listar professores.');
        }
    }

    public function show(Teacher $teacher)
    {
        try {
            $teacher = $this->service->show($teacher);

            return view(
                'pages.specialized-educational-support.teachers.show',
                compact('teacher')
            );

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao exibir professor.');
        }
    }

    public function create()
    {
        $disciplines = Discipline::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'pages.specialized-educational-support.teachers.create',
            compact('disciplines')
        );
    }

    public function store(TeacherRequest $request)
    {
        try {
            $this->service->create($request->validated());

            return redirect()
                ->route('specialized-educational-support.teachers.index')
                ->with('success', 'Professor cadastrado com sucesso.');

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao cadastrar professor.');
        }
    }

    public function edit(Teacher $teacher)
    {
        $disciplines = Discipline::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Pluck IDs para marcar os checkboxes/select no formulário
        $selectedDisciplines = $teacher->disciplines
            ->pluck('id')
            ->toArray();

        return view(
            'pages.specialized-educational-support.teachers.edit',
            compact('teacher', 'disciplines', 'selectedDisciplines')
        );
    }

    public function update(TeacherRequest $request, Teacher $teacher)
    {
        try {
            $this->service->update($teacher, $request->validated());

            return redirect()
                ->route('specialized-educational-support.teachers.index')
                ->with('success', 'Professor atualizado com sucesso.');

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao atualizar professor.');
        }
    }

    public function destroy(Teacher $teacher)
    {
        try {
            $this->service->delete($teacher);

            return redirect()
                ->route('specialized-educational-support.teachers.index')
                ->with('success', 'Professor removido com sucesso.');

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao remover professor.');
        }
    }

    /**
     * Exibe a tela de permissões GLOBAIS para todos os professores
     */
    public function permissions()
    {
        try {
            $permissions = Permission::all()->groupBy(function ($permission) {
                $prefix = explode('.', $permission->slug)[0];
                
                $translationKey = "permissions.entities.{$prefix}";
                $translated = __($translationKey);

                return $translated === $translationKey
                    ? ucfirst(str_replace('-', ' ', $prefix))
                    : $translated;
            });

            $globalPermissionsIds = $this->service->getGlobalPermissionsIds();

            return view(
                'pages.specialized-educational-support.teachers.global-permissions',
                compact('permissions', 'globalPermissionsIds')
            );

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao carregar permissões globais.');
        }
    }

    /**
     * Atualiza as permissões que todos os professores herdam
     */
    public function updatePermissions(Request $request)
    {
        try {
            $request->validate([
                'permissions'   => 'array',
                'permissions.*' => 'exists:permissions,id'
            ]);

            $this->service->updateGlobalPermissions($request->permissions ?? []);

            return redirect()
                ->route('specialized-educational-support.teachers.index')
                ->with('success', 'Permissões globais de professores atualizadas!');

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao atualizar permissões globais.');
        }
    }

    public function disciplines(Teacher $teacher)
    {
        $teacher->load(['person', 'courses', 'courseDisciplines.discipline']);

        $courses = Course::with(['disciplines' => function ($q) {
                $q->where('is_active', true)->orderBy('name');
            }])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedByCourse = $teacher->courseDisciplines
            ->groupBy('course_id')
            ->map(fn ($items) => $items->pluck('discipline_id')->toArray())
            ->toArray();

        return view(
            'pages.specialized-educational-support.teachers.disciplines',
            compact('teacher', 'courses', 'selectedByCourse')
        );
    }

    /**
     * Atualiza as disciplinas vinculadas ao professor
     */
    public function updateDisciplines(Request $request, Teacher $teacher)
    {
        try {
            $request->validate([
                'assignments' => 'array',
            ]);

            $this->service->syncGrade(
                $teacher,
                $request->assignments ?? []
            );

            return redirect()
                ->route('specialized-educational-support.teachers.show', $teacher)
                ->with('success', 'Matriz curricular atualizada com sucesso!');

        } catch (Throwable $e) {
            return $this->handleException($e, 'Erro ao atualizar matriz curricular.');
        }
    }
}