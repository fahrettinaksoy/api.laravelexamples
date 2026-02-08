<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Main\MainActionDestroy;
use App\Actions\Main\MainActionFilter;
use App\Actions\Main\MainActionShow;
use App\Actions\Main\MainActionStore;
use App\Actions\Main\MainActionUpdate;
use App\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseService
{
    protected Model $model;

    protected array $actions;

    public function __construct(
        protected BaseRepositoryInterface $repository,
    ) {
        $model = $this->repository->getModel();
        $this->model = $model;

        $this->actions = [
            'filter' => new MainActionFilter($this->repository),
            'show' => new MainActionShow($this->repository),
            'store' => new MainActionStore($this->repository),
            'update' => new MainActionUpdate($this->repository),
            'destroy' => new MainActionDestroy($this->repository),
        ];
    }

    public static function make(BaseRepositoryInterface $repository): self
    {
        return new self($repository);
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function filter(array $filter): mixed
    {
        return $this->actions['filter']->execute($filter);
    }

    public function show(array $filter): mixed
    {
        $id = request()->route('id');
        if ($id === null) {
            throw new \InvalidArgumentException('ID parametresi gereklidir.');
        }

        return $this->actions['show']->execute((int) $id);
    }

    public function store(array $data): Model
    {
        $created = DB::transaction(fn () => $this->actions['store']->execute($data));

        return $created->fresh() ?? $created;
    }

    public function update(array $data): Model
    {
        $id = request()->route('id');
        if ($id === null) {
            throw new \InvalidArgumentException('ID parametresi gereklidir.');
        }

        return DB::transaction(fn () => $this->actions['update']->execute((int) $id, $data));
    }

    public function destroy(array $data = []): mixed
    {
        $routeId = request()->route('id');
        $keyName = $this->model->getKeyName();

        if ($routeId === null && empty($data[$keyName]) && empty($data['ids'])) {
            throw new \InvalidArgumentException('Silme için route ID veya body içinde anahtar alan gereklidir.');
        }

        return DB::transaction(function () use ($routeId, $data, $keyName) {
            if ($routeId !== null) {
                return $this->actions['destroy']->execute((int) $routeId);
            }

            if (! empty($data['ids'])) {
                $data[$keyName] = $data['ids'];
                unset($data['ids']);
            }

            return $this->actions['destroy']->executeWithFilter($data);
        });
    }
}
