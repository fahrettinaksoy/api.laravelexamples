<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class BaseRepositoryCache implements BaseRepositoryInterface
{
    private const CACHE_TTL = 3600;

    protected string $cacheTag;

    public function __construct(
        private readonly BaseRepositoryInterface $repository
    ) {
        $modelClass = get_class($this->repository->getModel());
        $this->cacheTag = (new $modelClass)->getTable();
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $cacheKey = "{$this->cacheTag}.paginate.".md5(serialize($filters));

        return Cache::tags([$this->cacheTag])->remember(
            $cacheKey,
            self::CACHE_TTL,
            fn () => $this->repository->paginate($filters)
        );
    }

    public function findById(string $id): ?Model
    {
        return Cache::tags([$this->cacheTag])->remember(
            "{$this->cacheTag}.{$id}",
            self::CACHE_TTL,
            fn () => $this->repository->findById($id)
        );
    }

    public function all(array $filters = []): Collection
    {
        return $this->repository->all($filters);
    }

    public function create(array $data): Model
    {
        $result = $this->repository->create($data);
        $this->clearCache();

        return $result;
    }

    public function update(string $id, array $data): Model
    {
        $result = $this->repository->update($id, $data);
        $this->clearCache($id);

        return $result;
    }

    public function delete(string $id): bool
    {
        $result = $this->repository->delete($id);
        $this->clearCache($id);

        return $result;
    }

    public function findBy(string $field, mixed $value): ?Model
    {
        return $this->repository->findBy($field, $value);
    }

    public function getBy(string $field, mixed $value): Collection
    {
        return $this->repository->getBy($field, $value);
    }

    public function getModel(): Model
    {
        return $this->repository->getModel();
    }

    protected function clearCache(?string $id = null): void
    {
        Cache::tags([$this->cacheTag])->flush();
    }
}
