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

class TeacherController extends Controller
{
    protected TeacherService $service;

    public function __construct(TeacherService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
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
    }

    public function show(Teacher $teacher)
    {
        $teacher = $this->service->show($teacher);
        return view('pages.specialized-educational-support.teachers.show', compact('teacher'));
    }

    public function create()
    {
        $disciplines = Discipline::where('is_active', true)->orderBy('name')->get();

        return view(
            'pages.specialized-educational-support.teachers.create',
            compact('disciplines')
        );
    }

    public function store(TeacherRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('specialized-educational-support.teachers.index')
            ->with('success', 'Professor cadastrado com sucesso.');
    }

    public function edit(Teacher $teacher)
    {
        $disciplines = Discipline::where('is_active', true)->orderBy('name')->get();
        // Pluck IDs para marcar os checkboxes/select no formulário
        $selectedDisciplines = $teacher->disciplines->pluck('id')->toArray();

        return view(
            'pages.specialized-educational-support.teachers.edit',
            compact('teacher', 'disciplines', 'selectedDisciplines')
        );
    }

    public function update(TeacherRequest $request, Teacher $teacher)
    {
        $this->service->update($teacher, $request->validated());

        return redirect()
            ->route('specialized-educational-support.teachers.index')
            ->with('success', 'Professor atualizado com sucesso.');
    }

    public function destroy(Teacher $teacher)
    {
        $this->service->delete($teacher);

        return redirect()
            ->route('specialized-educational-support.teachers.index')
            ->with('success', 'Professor removido com sucesso.');
    }

    /**
     * Exibe a tela de permissões GLOBAIS para todos os professores
     */
    public function permissions()
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->slug)[0]; // módulo
        });

        $globalPermissionsIds = $this->service->getGlobalPermissionsIds();

        return view(
            'pages.specialized-educational-support.teachers.global-permissions',
            compact('permissions', 'globalPermissionsIds')
        );
    }

    /**
     * Atualiza as permissões que todos os professores herdam
     */
    public function updatePermissions(Request $request)
    {
        $request->validate([
            'permissions'   => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $this->service->updateGlobalPermissions($request->permissions ?? []);

        return redirect()
            ->route('specialized-educational-support.teachers.index')
            ->with('success', 'Permissões globais de professores atualizadas!');
    }

    public function disciplines(Teacher $teacher)
    {
        $teacher->load('person', 'disciplines');

        $courses = Course::with(['disciplines' => function ($q) {
                $q->where('is_active', true)->orderBy('name');
            }])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedDisciplinesIds = $teacher->disciplines->pluck('id')->toArray();

        return view(
            'pages.specialized-educational-support.teachers.disciplines',
            compact('teacher', 'courses', 'selectedDisciplinesIds')
        );
    }

    /**
     * Atualiza as disciplinas vinculadas ao professor
     */
    public function updateDisciplines(Request $request, Teacher $teacher)
    {
        $request->validate([
            'disciplines'   => 'array',
            'disciplines.*' => 'exists:disciplines,id'
        ]);

        $this->service->syncDisciplines($teacher, $request->disciplines ?? []);

        return redirect()
            ->route('specialized-educational-support.teachers.show', $teacher)
            ->with('success', 'Grade curricular atualizada com sucesso!');
    }
}