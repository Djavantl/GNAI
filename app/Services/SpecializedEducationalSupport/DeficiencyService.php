<?php


namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Deficiency;
use Illuminate\Support\Facades\DB;

class DeficiencyService
{
    public function index(array $filters = [])
    {
        return Deficiency::query()
            ->name($filters['name'] ?? null)
            ->cid($filters['cid_code'] ?? null)
            ->active($filters['is_active'] ?? null)
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();
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

    public function listActiveOrdered()
    {
        return Deficiency::where('is_active', true)->orderBy('name')->get();
    }

}
