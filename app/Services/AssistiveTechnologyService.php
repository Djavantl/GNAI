<?php

namespace App\Services;

use App\Models\AssistiveTechnology;
use Illuminate\Support\Facades\DB;

class AssistiveTechnologyService
{
    public function __construct(
        protected AssistiveTechnologyImageService $imageService
    ) {}

    public function listAll()
    {
        return AssistiveTechnology::with(['status', 'deficiencies', 'images'])
            ->orderBy('name')
            ->get();
    }

    public function store(array $data): AssistiveTechnology
    {
        return DB::transaction(function () use ($data) {
            $tech = AssistiveTechnology::create($data);

            if (isset($data['deficiencies'])) {
                $tech->deficiencies()->sync($data['deficiencies']);
            }

            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $imageFile) {
                    $this->imageService->store($tech, $imageFile);
                }
            }

            return $tech;
        });
    }

    public function update(AssistiveTechnology $tech, array $data): AssistiveTechnology
    {
        return DB::transaction(function () use ($tech, $data) {
            $tech->update($data);

            if (isset($data['deficiencies'])) {
                $tech->deficiencies()->sync($data['deficiencies']);
            }

            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $imageFile) {
                    $this->imageService->store($tech, $imageFile);
                }
            }

            return $tech;
        });
    }

    public function toggleActive(AssistiveTechnology $tech): AssistiveTechnology
    {
        return DB::transaction(function () use ($tech) {
            $tech->update(['is_active' => ! $tech->is_active]);
            return $tech;
        });
    }

    public function delete(AssistiveTechnology $tech): void
    {
        DB::transaction(function () use ($tech) {
            $tech->delete();
        });
    }
}
