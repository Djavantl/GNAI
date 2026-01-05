<?php

namespace App\Services;

use App\Models\Person;
use Illuminate\Support\Facades\DB;

class PersonService
{
    public function listAll()
    {
        return Person::latest()->get();
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Person::create($data);
        });
    }

    public function update(Person $person, array $data)
    {  
        $person->update($data);
        return $person;
    }

    public function delete(Person $person)
    {
        return $person->delete();
    }
}