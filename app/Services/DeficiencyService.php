<?php


namespace App\Services;

use App\Models\Deficiency;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class DeficiencyService
{
    public function listAll()
    {
        return Deficiency::orderBy('name')->get();
    }


    public function store(array $data): Deficiency
    {
        return DB::transaction(fn() => Deficiency::create($data)
        );
    }

    public function update(Deficiency $deficiency, array $data): Deficiency
    {
        return DB::transaction(function () use ($deficiency, $data) {
            $deficiency->update($data);
            return $deficiency;
        });
    }

    public function toggleActive(Deficiency $deficiency): Deficiency
    {
        return DB::transaction(function () use ($deficiency) {
            $deficiency->update(['is_active' => ! $deficiency->is_active]);

            return $deficiency;
        });
    }

    public function delete(Deficiency $deficiency): void
    {
        DB::transaction(function () use ($deficiency) {
            $deficiency->delete();
        });
    }
}
