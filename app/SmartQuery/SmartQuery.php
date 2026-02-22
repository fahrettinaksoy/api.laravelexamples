<?php

declare(strict_types=1);

namespace App\SmartQuery;

use App\SmartQuery\Concerns\FiltersQuery;
use App\SmartQuery\Concerns\IncludesRelationships;
use App\SmartQuery\Concerns\SelectsFields;
use App\SmartQuery\Concerns\SortsQuery;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Traits\ForwardsCalls;

class SmartQuery
{
    use FiltersQuery;
    use ForwardsCalls;
    use IncludesRelationships;
    use SelectsFields;
    use SortsQuery;

    protected Model $model;

    protected EloquentBuilder|QueryBuilder $builder;

    protected bool $useEloquent = true;

    protected ?Request $request = null;

    protected ?string $mapToClass = null;

    protected bool $hydrateToModel = false;

    public static function for($subject, ?Request $request = null): static
    {
        $instance = new static;
        $instance->request = $request ?? request();

        if (is_string($subject) && is_subclass_of($subject, Model::class)) {
            $instance->model = new $subject;
            $instance->builder = $subject::query();
            $instance->useEloquent = true;
        } elseif ($subject instanceof EloquentBuilder) {
            $instance->builder = $subject;
            $instance->model = $subject->getModel();
            $instance->useEloquent = true;
        } elseif ($subject instanceof Model) {
            $instance->model = $subject;
            $instance->builder = $subject->newQuery();
            $instance->useEloquent = true;
        } elseif ($subject instanceof QueryBuilder) {
            $instance->builder = $subject;
            $instance->useEloquent = false;
        } else {
            throw new \InvalidArgumentException(
                'Subject must be a Model class, Model instance, Eloquent Builder, or Query Builder',
            );
        }

        return $instance;
    }

    public function useRawQueries(): static
    {
        if (! isset($this->model)) {
            throw new \RuntimeException('Cannot switch to raw mode without a model');
        }

        $this->builder = DB::table($this->model->getTable());
        $this->useEloquent = false;

        return $this;
    }

    public function useEloquentQueries(): static
    {
        if (! isset($this->model)) {
            throw new \RuntimeException('Cannot switch to eloquent mode without a model');
        }

        $this->builder = $this->model->newQuery();
        $this->useEloquent = true;

        return $this;
    }

    public function mapTo(string $dtoClass): static
    {
        $this->mapToClass = $dtoClass;

        return $this;
    }

    public function asModel(): static
    {
        $this->hydrateToModel = true;

        return $this;
    }

    public function getBuilder(): EloquentBuilder|QueryBuilder
    {
        return $this->builder;
    }

    public function getModel(): ?Model
    {
        return $this->model ?? null;
    }

    public function isEloquent(): bool
    {
        return $this->useEloquent;
    }

    public function __call(string $method, array $parameters)
    {
        return $this->forwardCallTo($this->builder, $method, $parameters);
    }
}
