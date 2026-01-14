<?php


namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\AssistiveTechnologyStatus;
use Illuminate\Support\Facades\DB;

class AssistiveTechnologyStatusService
{
    public function listAll()
    {
        return AssistiveTechnologyStatus::orderBy('name')->get();
    }


    public function store(array $data): AssistiveTechnologyStatus
    {
        return DB::transaction(fn() => AssistiveTechnologyStatus::create($data)
        );
    }

    public function update(AssistiveTechnologyStatus $status,array $data): AssistiveTechnologyStatus
    {
        return DB::transaction(function () use ($status, $data) {
            $status->update($data);
            return $status;
        });
    }

    public function toggleActive(AssistiveTechnologyStatus $status): AssistiveTechnologyStatus
    {
        return DB::transaction(function () use ($status) {
            $status->update(['is_active' => ! $status->is_active]);
            return $status;
        });
    }

    public function delete(AssistiveTechnologyStatus $status): void
    {
        DB::transaction(function () use ($status) {
            $status->delete();
        });
    }
}
