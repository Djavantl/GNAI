<?php


namespace App\Services\SpecializedEducationalSupport;

use App\Domains\Auth\Application\Permissions\PermissionCache;
use App\Models\SpecializedEducationalSupport\Position;
use Illuminate\Support\Facades\DB;

class PositionService
{
    public function __construct(
        private readonly PermissionCache $permissionCache,
    ) {}

    public function index(array $filters = [])
    {
        return Position::query()
            ->name($filters['name'] ?? null)
            ->description($filters['description'] ?? null)
            ->active($filters['is_active'] ?? null)

            ->withCount('professionals')
            ->orderBy('name')

            ->paginate(10)
            ->withQueryString();
    }

    public function store(array $data): Position
    {
        return DB::transaction(function () use ($data) {

            $permissions = $data['permissions'] ?? [];
            unset($data['permissions']);

            $position = Position::create($data);

            if (!empty($permissions)) {
                $position->permissions()->sync($permissions);
            }

            $this->permissionCache->invalidate();

            return $position;
        });
    }

    public function update(Position $position, array $data): Position
    {
        return DB::transaction(function () use ($position, $data) {

            $permissions = $data['permissions'] ?? [];
            unset($data['permissions']);

            $position->update($data);
            $position->permissions()->sync($permissions);

            $this->permissionCache->invalidate();

            return $position;
        });
    }

    public function toggleActive(Position $position): Position
    {
        return DB::transaction(function () use ($position) {

            if ($position->is_active) {
                $position->ensureCanBeDeactivated();
            }

            $position->update([
                'is_active' => ! $position->is_active
            ]);

            return $position;
        });
    }

    public function delete(Position $position): void
    {
        DB::transaction(function () use ($position) {
            $position->ensureCanBeDeleted();
            
            $position->delete();
        });
    }
}
