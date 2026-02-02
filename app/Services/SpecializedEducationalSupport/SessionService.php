<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Session;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SessionService
{
    //listar all

    public function index()
    {
        return Session::get();
    }

    // criar

    public function create(array $data): Session
    {
        return DB::transaction(function () use ($data) {
            return Session::create($data);
        });
    }

    // mostrar somente uma

    public function show(Session $session): Session
    {
        return $session->load(['student', 'professional']);
    }

    //atualizar

    public function update(Session $session, array $data): Session
    {
        return DB::transaction(function () use ($session, $data) {
            $session->update($data);

            return $session;
        });
    }

    //soft delete

    public function delete(Session $session): void
    {
        $session->delete();
    }

    //restaurar

    public function restore(Session $session): Session
    {
        $session->restore();

        return $session;
    }

    //excluir definitivamente

    public function forceDelete(Session $session): void
    {
        $session->forceDelete();
    }
}
