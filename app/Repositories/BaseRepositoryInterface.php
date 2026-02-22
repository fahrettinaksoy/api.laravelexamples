<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    public function paginate(array $queryContext = []): LengthAwarePaginator;

    public function findById(int $id, array $includes = []): ?Model;

    public function all(array $queryContext = []): Collection;

    public function create(array $data): Model;

    public function update(int $id, array $data): Model;

    public function delete(int $id): bool;

    public function deleteMany(array $criteria): int;

    public function findBy(string $field, mixed $value): ?Model;

    public function getBy(string $field, mixed $value): Collection;
}
