<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\SessionRecord;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SessionRecordService
{
    //listar all

    public function index()
    {
        return SessionRecord::get();
    }

    // criar

    public function create(array $data): SessionRecord
    {
        return DB::transaction(function () use ($data) {
            return SessionRecord::create($data);
        });
    }

    // mostrar somente uma

    public function show(SessionRecord $session_rec): SessionRecord
    {
        return $session_rec->load(['session']);
    }

    //atualizar

    public function update(SessionRecord $session_rec, array $data): SessionRecord
    {
        return DB::transaction(function () use ($session_rec, $data) {
            $session_rec->update($data);

            return $session_rec;
        });
    }

    //soft delete

    public function delete(SessionRecord $session_rec): void
    {
        $session_rec->delete();
    }

    //restaurar

    public function restore(SessionRecord $session_rec): SessionRecord
    {
        $session_rec->restore();

        return $session_rec;
    }

    //excluir definitivamente

    public function forceDelete(SessionRecord $session_rec): void
    {
        $session_rec->forceDelete();
    }
}
