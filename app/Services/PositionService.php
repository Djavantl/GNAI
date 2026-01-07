<?php


namespace App\Services;

use App\Models\Position;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class PositionService
{
    public function listAll()
    {
        return Position::orderBy('name')->get();
    }


    public function store(array $data): Position
    {
        return DB::transaction(fn() => Position::create($data)
        );
    }

    public function update(Position $position, array $data): Position
    {
        return DB::transaction(function () use ($position, $data) {
            $position->update($data);
            return $position;
        });
    }

    public function toggleActive(Position $position): Position
    {
        return DB::transaction(function () use ($position) {
            $position->update(['is_active' => ! $position->is_active]);

            return $position;
        });
    }

    public function delete(Position $position): void
    {
        DB::transaction(function () use ($position) {
            $position->delete();
        });
    }
}
