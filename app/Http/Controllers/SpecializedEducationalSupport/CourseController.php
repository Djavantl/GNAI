<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\SpecializedEducationalSupport\Course;
use App\Http\Requests\SpecializedEducationalSupport\CourseRequest;
use App\Models\SpecializedEducationalSupport\Discipline;
use App\Services\SpecializedEducationalSupport\CourseService;
use Illuminate\Http\Request;
use Exception;

class CourseController extends Controller
{
    protected CourseService $service;

    public function __construct(CourseService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {
            $courses = $this->service->index($request->all());

            if ($request->ajax()) {
                return view('pages.specialized-educational-support.courses.partials.table', compact('courses'))->render();
            }

            return view('pages.specialized-educational-support.courses.index', compact('courses'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar listagem: ' . $e->getMessage());
        }
    }

    public function show(Course $course)
    {
        try {
            $course = $this->service->show($course);
            return view('pages.specialized-educational-support.courses.show', compact('course'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao exibir curso: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $disciplines = Discipline::where('is_active', true)->orderBy('name')->get();
            return view('pages.specialized-educational-support.courses.create', compact('disciplines'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar formulário de criação: ' . $e->getMessage());
        }
    }

    public function store(CourseRequest $request)
    {
        try {
            $this->service->create($request->validated());

            return redirect()
                ->route('specialized-educational-support.courses.index')
                ->with('success', 'Curso cadastrado com sucesso.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao cadastrar curso: ' . $e->getMessage());
        }
    }

    public function edit(Course $course)
    {
        try {
            $disciplines = Discipline::where('is_active', true)->orderBy('name')->get();
            $course->load('disciplines');
            return view('pages.specialized-educational-support.courses.edit', compact('course', 'disciplines'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro ao carregar formulário de edição: ' . $e->getMessage());
        }
    }

    public function update(CourseRequest $request, Course $course)
    {
        try {
            $this->service->update($course, $request->validated());

            return redirect()
                ->route('specialized-educational-support.courses.index')
                ->with('success', 'Curso atualizado com sucesso.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar curso: ' . $e->getMessage());
        }
    }

    public function destroy(Course $course)
    {
        try {
            $this->service->delete($course);

            return redirect()
                ->route('specialized-educational-support.courses.index')
                ->with('success', 'Curso removido com sucesso.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erro ao remover curso: ' . $e->getMessage());
        }
    }
}