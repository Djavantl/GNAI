<?php

namespace App\Http\Controllers\SpecializedEducationalSupport;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecializedEducationalSupport\StorePersonRequest;
use App\Models\SpecializedEducationalSupport\Person;
use App\Services\SpecializedEducationalSupport\PersonService;

class PersonController extends Controller
{
    protected PersonService $service;

    public function __construct(PersonService $service){
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $people = $this->service->listAll();
        return view('pages.specialized-educational-support.people.index', compact('people'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.specialized-educational-support.people.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonRequest $request)
    {
        $this->service->store($request->validated());

        return redirect()->route('specialized-educational-support.people.index')->with('success', 'Pessoa Criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Person $person)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Person $person)
    {
        return view('specialized-educational-support.people.edit', compact('person'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePersonRequest $request, Person $person)
    {
        $this->service->update($person, $request->validated());

        return redirect()->route('specialized-educational-support.people.index')->with('success', 'Cadastro atualizado!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Person $person)
    {
        $this->service->delete($person);

        return redirect()->route('specialized-educational-support.people.index')->with('success', 'Registro removido!');
    }
}
