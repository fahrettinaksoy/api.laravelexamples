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
        protected readonly BaseRepositoryInterface $repository,
        string $cacheTag,
    ) {
        $this->cacheTag = $cacheTag;
    }

    public function paginate(array $queryContext = []): LengthAwarePaginator
    {
        $cacheKey = "{$this->cacheTag}.paginate." . md5(json_encode($queryContext, JSON_THROW_ON_ERROR));

        return Cache::tags([$this->cacheTag])->remember(
            $cacheKey,
            self::CACHE_TTL,
            fn () => $this->repository->paginate($queryContext),
        );
    }

    public function findById(int $id, array $includes = []): ?Model
    {
        $includeKey = ! empty($includes) ? '.' . md5(implode(',', $includes)) : '';

        return Cache::tags([$this->cacheTag])->remember(
            "{$this->cacheTag}.{$id}{$includeKey}",
            self::CACHE_TTL,
            fn () => $this->repository->findById($id, $includes),
        );
    }

    public function all(array $queryContext = []): Collection
    {
        $cacheKey = "{$this->cacheTag}.all." . md5(json_encode($queryContext, JSON_THROW_ON_ERROR));

        return Cache::tags([$this->cacheTag])->remember(
            $cacheKey,
            self::CACHE_TTL,
            fn () => $this->repository->all($queryContext),
        );
    }

    public function create(array $data): Model
    {
        $result = $this->repository->create($data);
        Cache::tags([$this->cacheTag])->flush();

        return $result;
    }

    public function update(int $id, array $data): Model
    {
        $result = $this->repository->update($id, $data);
        Cache::tags([$this->cacheTag])->flush();

        return $result;
    }

    public function delete(int $id): bool
    {
        $result = $this->repository->delete($id);
        Cache::tags([$this->cacheTag])->flush();

        return $result;
    }

    public function deleteMany(array $criteria): int
    {
        $result = $this->repository->deleteMany($criteria);
        Cache::tags([$this->cacheTag])->flush();

        return $result;
    }

    public function findBy(string $field, mixed $value): ?Model
    {
        $cacheKey = "{$this->cacheTag}.findBy.{$field}." . md5(serialize($value));

        return Cache::tags([$this->cacheTag])->remember(
            $cacheKey,
            self::CACHE_TTL,
            fn () => $this->repository->findBy($field, $value),
        );
    }

    public function getBy(string $field, mixed $value): Collection
    {
        $cacheKey = "{$this->cacheTag}.getBy.{$field}." . md5(serialize($value));

        return Cache::tags([$this->cacheTag])->remember(
            $cacheKey,
            self::CACHE_TTL,
            fn () => $this->repository->getBy($field, $value),
        );
    }
}
