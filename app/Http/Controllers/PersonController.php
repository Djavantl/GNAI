<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Services\PersonService;
use App\Http\Requests\StorePersonRequest;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    protected $service;

    public function __construct(PersonService $service){
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $people = $this->service->listAll();
        return view('people.index', compact('people'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('people.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonRequest $request)
    {
        $this->service->store($request->validated());

        return redirect()->route('people.index')->with('success', 'Pessoa Criada com sucesso!');
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
        return view('people.edit', compact('person'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePersonRequest $request, Person $person)
    {
        $this->service->update($person, $request->validated());

        return redirect()->route('people.index')->with('success', 'Cadastro atualizado!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Person $person)
    {
        $this->service->delete($person);

        return redirect()->route('people.index')->with('success', 'Registro removido!');
    }
}
