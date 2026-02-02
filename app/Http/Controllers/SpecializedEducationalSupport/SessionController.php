<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Models\SpecializedEducationalSupport\Session;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Services\SpecializedEducationalSupport\SessionService;
use App\Http\Requests\SpecializedEducationalSupport\SessionRequest;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    protected SessionService $service;

    public function __construct(SessionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $sessions = $this->service->index();
        return view('specialized-educational-support.sessions.index', compact('sessions'));
    }

    public function create()
    {
        $students = Student::all();
        $professionals = Professional::all();

        // dd($students->toArray(), $professionals->toArray());
        return view('specialized-educational-support.sessions.create', compact('students', 'professionals'));
    }

    public function store(SessionRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('specialized-educational-support.sessions.index')
            ->with('success', 'Sessão agendada com sucesso.');
    }

    public function show(Session $session)
    {
        $session = $this->service->show($session);
        return view('specialized-educational-support.sessions.show', compact('session'));
    }

    public function edit(Session $session)
    {
        return view('specialized-educational-support.sessions.edit', compact('session'));
    }

    public function update(Session $session, SessionRequest $request)
    {
        $this->service->update($session, $request->validated());

        return redirect()
            ->route('specialized-educational-support.sessions.show', $session)
            ->with('success', 'Sessão atualizada com sucesso.');
    }

    public function destroy(Session $session)
    {
        $this->service->delete($session);

        return redirect()
            ->route('specialized-educational-support.sessions.index')
            ->with('success', 'Sessão removida com sucesso.');
    }

    public function restore(Session $session)
    {
        $this->service->restore($session);

        return redirect()
            ->route('specialized-educational-support.sessions.index')
            ->with('success', 'Sessão restaurada com sucesso.');
    }

    public function forceDelete(Session $session)
    {
        $this->service->forceDelete($session);

        return redirect()->back()->with('success', 'Removido permanentemente.');
    }
}
