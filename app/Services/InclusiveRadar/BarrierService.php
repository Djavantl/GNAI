<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\Barrier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class BarrierService
{
    public function __construct(
        protected BarrierImageService $imageService
    ) {}

    public function listAll()
    {
        return Barrier::with([
            'category',
            'status',
            'location',
            'deficiencies',
            'images',
            'user'
        ])->latest()->paginate(10);
    }

    public function store(array $data): Barrier
    {
        return DB::transaction(function () use ($data) {
            $data['identified_at'] = $data['identified_at'] ?? Carbon::now()->toDateString();

            if (Auth::check()) {
                $user = Auth::user();
                $data['user_id'] = $user->id;
                $data['reporter_role'] = $user->student ? 'Estudante' : 'Servidor/Comunidade';
            }

            $data['is_anonymous'] = !empty($data['is_anonymous']);
            $data['is_active'] = $data['is_active'] ?? true;

            if (!empty($data['no_location'])) {
                $data['location_id'] = null;
                $data['latitude'] = null;
                $data['longitude'] = null;
            }

            $barrier = Barrier::create($data);

            if (isset($data['deficiencies'])) {
                $barrier->deficiencies()->sync($data['deficiencies']);
            }

            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $imageFile) {
                    $this->imageService->store($barrier, $imageFile);
                }
            }

            return $barrier;
        });
    }

    public function update(Barrier $barrier, array $data): Barrier
    {
        return DB::transaction(function () use ($barrier, $data) {

            $data['is_anonymous'] = !empty($data['is_anonymous']);
            $data['is_active'] = isset($data['is_active']) ? (bool)$data['is_active'] : $barrier->is_active;

            if (!empty($data['no_location'])) {
                $data['location_id'] = null;
                $data['latitude'] = null;
                $data['longitude'] = null;
            }

            $barrier->update($data);

            if (isset($data['deficiencies'])) {
                $barrier->deficiencies()->sync($data['deficiencies']);
            }

            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $imageFile) {
                    $this->imageService->store($barrier, $imageFile);
                }
            }

            return $barrier;
        });
    }

    public function toggleActive(Barrier $barrier): Barrier
    {
        return DB::transaction(function () use ($barrier) {
            $barrier->update([
                'is_active' => ! $barrier->is_active
            ]);
            return $barrier;
        });
    }

    public function delete(Barrier $barrier): void
    {
        DB::transaction(function () use ($barrier) {
            $barrier->delete();
        });
    }
}
