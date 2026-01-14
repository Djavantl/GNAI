<?php

namespace App\Services;

use App\Models\BarrierStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class BarrierStatusService
{
    public function listAll()
    {
        return BarrierStatus::orderBy('name')->get();
    }


    public function store(array $data): BarrierStatus
    {
        return DB::transaction(fn() => BarrierStatus::create($data)
        );
    }

    public function update(BarrierStatus $status, array $data): BarrierStatus
    {
        return DB::transaction(function () use ($status, $data) {
            $status->update($data);
            return $status;
        });
    }

    public function toggleActive(BarrierStatus $status): BarrierStatus
    {
        return DB::transaction(function () use ($status) {
            $status->update(['is_active' => !$status->is_active]);
            return $status;
        });
    }

    public function delete(BarrierStatus $status): void
    {
        DB::transaction(function () use ($status) {
            $status->delete();
        });
    }

}




