<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Main\MainDestroyAction;
use App\Actions\Main\MainIndexAction;
use App\Actions\Main\MainShowAction;
use App\Actions\Main\MainStoreAction;
use App\Actions\Main\MainUpdateAction;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BaseService
{
    public function __construct(
        protected BaseRepositoryInterface $repository,
        protected array $actions = [],
    ) {
        $defaults = [
            'index' => new MainIndexAction($this->repository),
            'show' => new MainShowAction($this->repository),
            'store' => new MainStoreAction($this->repository),
            'update' => new MainUpdateAction($this->repository),
            'destroy' => new MainDestroyAction($this->repository),
        ];

        $this->actions = array_merge($defaults, $this->actions);
    }

    public function index(array $queryContext = []): LengthAwarePaginator
    {
        return $this->actions['index']->execute($queryContext);
    }

    public function show(int $id, array $includes = []): Model
    {
        return $this->actions['show']->execute($id, $includes);
    }

    public function store(array $data): Model
    {
        return DB::transaction(fn () => $this->actions['store']->execute($data));
    }

    public function update(int $id, array $data): Model
    {
        return DB::transaction(fn () => $this->actions['update']->execute($id, $data));
    }

    public function destroy(int $id): bool
    {
        return DB::transaction(fn () => $this->actions['destroy']->execute($id));
    }

    public function destroyMany(array $criteria): int
    {
        return DB::transaction(fn () => $this->actions['destroy']->executeWithFilter($criteria));
    }
}
