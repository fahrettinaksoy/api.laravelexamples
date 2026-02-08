<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator;

    public function findById(string $id): ?Model;

    public function all(array $filters = []): Collection;

    public function create(array $data): Model;

    public function update(string $id, array $data): Model;

    public function delete(string $id): bool;

    public function findBy(string $field, mixed $value): ?Model;

    public function getBy(string $field, mixed $value): Collection;

    public function getModel(): Model;
}
