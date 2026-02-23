<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Traits\HasCacheStrategy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseRepositoryCache implements BaseRepositoryInterface
{
    use HasCacheStrategy;

    public function __construct(
        protected readonly BaseRepositoryInterface $repository,
        string $cacheTag,
    ) {
        $this->cacheTag = $cacheTag;
        $this->cacheTtl = (int) config('cache.repository_ttl', 3600);
    }

    public function paginate(array $queryContext = []): LengthAwarePaginator
    {
        return $this->cached('paginate', $queryContext, fn () => $this->repository->paginate($queryContext));
    }

    public function findById(int $id, array $includes = []): ?Model
    {
        return $this->cached("findById.{$id}", $includes, fn () => $this->repository->findById($id, $includes));
    }

    public function all(array $queryContext = []): Collection
    {
        return $this->cached('all', $queryContext, fn () => $this->repository->all($queryContext));
    }

    public function create(array $data): Model
    {
        return $this->invalidating(fn () => $this->repository->create($data));
    }

    public function update(int $id, array $data): Model
    {
        return $this->invalidating(fn () => $this->repository->update($id, $data));
    }

    public function delete(int $id): bool
    {
        return $this->invalidating(fn () => $this->repository->delete($id));
    }

    public function deleteMany(array $criteria): int
    {
        return $this->invalidating(fn () => $this->repository->deleteMany($criteria));
    }

    public function findBy(string $field, mixed $value): ?Model
    {
        return $this->cached("findBy.{$field}", $value, fn () => $this->repository->findBy($field, $value));
    }

    public function getBy(string $field, mixed $value): Collection
    {
        return $this->cached("getBy.{$field}", $value, fn () => $this->repository->getBy($field, $value));
    }
}
