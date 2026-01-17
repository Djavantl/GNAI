<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\SpecializedEducationalSupport\Deficiency;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AssistiveTechnologyService
{
    public function __construct(
        protected AssistiveTechnologyImageService $imageService,
        protected ResourceAttributeValueService $attributeValueService
    ) {}

    public function listAll(): Collection
    {
        return AssistiveTechnology::with([
            'type',
            'resourceStatus',
            'deficiencies',
            'images',
        ])
            ->orderBy('name')
            ->get();
    }

    public function getCreateData(): array
    {
        return [
            'deficiencies' => Deficiency::orderBy('name')->get(),
        ];
    }

    public function getEditData(AssistiveTechnology $assistiveTechnology): array
    {
        $assistiveTechnology->load([
            'deficiencies',
            'images',
        ]);

        return [
            'assistiveTechnology' => $assistiveTechnology,
            'attributeValues' => [],
        ];
    }

    public function store(array $data): AssistiveTechnology
    {
        return DB::transaction(function () use ($data) {
            $assistiveTechnology = AssistiveTechnology::create($data);

            if (!empty($data['deficiencies'])) {
                $assistiveTechnology->deficiencies()->sync($data['deficiencies']);
            }

            $this->attributeValueService->saveValues(
                'assistive_technology',
                $assistiveTechnology->id,
                $data['attributes'] ?? []
            );

            if (!empty($data['images'])) {
                foreach ($data['images'] as $imageFile) {
                    $this->imageService->store($assistiveTechnology, $imageFile);
                }
            }

            return $assistiveTechnology;
        });
    }

    public function update(AssistiveTechnology $assistiveTechnology, array $data): AssistiveTechnology
    {
        return DB::transaction(function () use ($assistiveTechnology, $data) {
            $assistiveTechnology->update($data);

            if (array_key_exists('deficiencies', $data)) {
                $assistiveTechnology->deficiencies()->sync($data['deficiencies'] ?? []);
            }

            $this->attributeValueService->saveValues(
                'assistive_technology',
                $assistiveTechnology->id,
                $data['attributes'] ?? []
            );

            if (!empty($data['images'])) {
                foreach ($data['images'] as $imageFile) {
                    $this->imageService->store($assistiveTechnology, $imageFile);
                }
            }

            return $assistiveTechnology;
        });
    }

    public function toggleActive(AssistiveTechnology $assistiveTechnology): AssistiveTechnology
    {
        return DB::transaction(function () use ($assistiveTechnology) {
            $assistiveTechnology->update([
                'is_active' => ! $assistiveTechnology->is_active,
            ]);

            return $assistiveTechnology;
        });
    }

    public function delete(AssistiveTechnology $assistiveTechnology): void
    {
        DB::transaction(function () use ($assistiveTechnology) {
            $assistiveTechnology->delete();
        });
    }
}
