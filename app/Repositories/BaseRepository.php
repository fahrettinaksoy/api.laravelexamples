<?php

declare(strict_types=1);

namespace App\Repositories;

use App\SmartQuery\SmartQuery;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;

        return SmartQuery::for($this->model)
            ->allowedFilters($this->model->allowedFiltering)
            ->allowedSorts($this->model->allowedSorting)
            ->allowedIncludes($this->model->allowedRelations)
            ->defaultSort($this->model->defaultSorting)
            ->paginate($perPage);
    }

    public function findById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function all(array $filters = []): Collection
    {
        return SmartQuery::for($this->model)
            ->allowedFilters($this->model->allowedFiltering)
            ->allowedSorts($this->model->allowedSorting)
            ->allowedIncludes($this->model->allowedRelations)
            ->defaultSort($this->model->defaultSorting)
            ->get();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Model
    {
        $item = $this->findById($id);

        if ($item) {
            $item->update($data);

            return $item->fresh();
        }

        throw new \RuntimeException("Record with ID {$id} not found");
    }

    public function delete(int $id): bool
    {
        $item = $this->findById($id);

        if ($item) {
            return $item->delete();
        }

        return false;
    }

    public function findBy(string $field, mixed $value): ?Model
    {
        return $this->model->where($field, $value)->first();
    }

    public function getBy(string $field, mixed $value): Collection
    {
        return $this->model->where($field, $value)->get();
    }

    public function getModel(): Model
    {
        return $this->model;
    }
}
