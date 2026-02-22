<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Traits\HasSmartQuery;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseRepository implements BaseRepositoryInterface
{
    use HasSmartQuery;

    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function paginate(array $queryContext = []): LengthAwarePaginator
    {
        $perPage = $queryContext['limit'] ?? $queryContext['per_page'] ?? 15;

        return $this->buildSmartQuery()
            ->paginate($perPage);
    }

    public function findById(int $id, array $includes = []): ?Model
    {
        $query = $this->model->newQuery();

        $resolved = $this->resolveIncludes($includes);

        if (! empty($resolved)) {
            $query->with($resolved);
        }

        return $query->findOrFail($id);
    }

    public function all(array $queryContext = []): Collection
    {
        return $this->buildSmartQuery()
            ->get();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Model
    {
        $item = $this->model->newQuery()->findOrFail($id);
        $item->update($data);

        return $item->refresh();
    }

    public function delete(int $id): bool
    {
        $item = $this->model->newQuery()->find($id);

        if ($item) {
            return $item->delete();
        }

        return false;
    }

    public function deleteMany(array $criteria): int
    {
        $query = $this->model->newQuery();

        if (! empty($criteria['ids'])) {
            $criteria[$this->model->getKeyName()] = $criteria['ids'];
            unset($criteria['ids']);
        }

        foreach ($criteria as $field => $value) {
            is_array($value)
                ? $query->whereIn($field, $value)
                : $query->where($field, $value);
        }

        return $query->delete();
    }

    public function findBy(string $field, mixed $value): ?Model
    {
        return $this->model->where($field, $value)->first();
    }

    public function getBy(string $field, mixed $value): Collection
    {
        return $this->model->where($field, $value)->get();
    }
}
