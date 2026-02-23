<?php

declare(strict_types=1);

namespace App\Support\RouteResolver;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PivotRouteResolver
{
    public function __construct(
        private readonly StandardRouteResolver $standardResolver,
    ) {}

    public static function isPivotRoute(array $pathSegments): bool
    {
        if (count($pathSegments) < 3) {
            return false;
        }

        $segmentCount = count($pathSegments);

        for ($i = 0; $i < $segmentCount - 1; $i++) {
            if (is_numeric($pathSegments[$i])
                && ! is_numeric($pathSegments[$i + 1])
                && preg_match('/^[a-zA-Z_-]+$/', $pathSegments[$i + 1])
            ) {
                return true;
            }
        }

        return false;
    }

    public function resolve(array $pathSegments): array
    {
        $pivotLevels = $this->extractPivotLevels($pathSegments);

        if (empty($pivotLevels)) {
            throw new NotFoundHttpException(__('api.route.invalid_pivot'));
        }

        $deepestLevel = end($pivotLevels);
        $mainModelPath = array_slice($pathSegments, 0, $pivotLevels[0]['parentIdIndex']);
        $finalModelClass = $this->resolveDeepPivotModel($pivotLevels);

        return [
            'isPivotRoute' => true,
            'parentModelClass' => $deepestLevel['parentModelClass'],
            'pivotModelClass' => $finalModelClass,
            'relationName' => $deepestLevel['relationName'],
            'originalRelationName' => $deepestLevel['originalRelation'],
            'parentId' => $deepestLevel['parentId'],
            'relationId' => $deepestLevel['relationId'] ?? null,
            'mainModelPath' => implode('/', $mainModelPath),
            'tableName' => end($mainModelPath),
            'pivotTableName' => $this->extractTableName($finalModelClass),
            'modelClass' => $finalModelClass,
            'fullPath' => implode('/', $pathSegments),
        ];
    }

    private function extractPivotLevels(array $pathSegments): array
    {
        $levels = [];
        $currentModelClass = null;
        $currentPath = [];
        $segmentCount = count($pathSegments);

        $i = 0;
        while ($i < $segmentCount) {
            if (! is_numeric($pathSegments[$i])) {
                $currentPath[] = $pathSegments[$i];
                $i++;

                continue;
            }

            if (! isset($pathSegments[$i + 1]) || is_numeric($pathSegments[$i + 1])) {
                $i++;

                continue;
            }

            $level = $this->buildPivotLevel($pathSegments, $i, $currentPath, $currentModelClass);
            $levels[] = $level;

            $currentModelClass = $this->getRelationModelClass(
                $level['parentModelClass'],
                $level['relationName'],
            );

            $i += 2;
        }

        return $levels;
    }

    private function buildPivotLevel(
        array $pathSegments,
        int $index,
        array $currentPath,
        ?string $currentModelClass,
    ): array {
        $parentId = (int) $pathSegments[$index];
        $originalRelation = $pathSegments[$index + 1];
        $relationName = Str::snake($originalRelation);

        $parentModelClass = $currentModelClass ?? $this->standardResolver->buildModelClass($currentPath);

        $relationId = null;
        if (isset($pathSegments[$index + 2]) && is_numeric($pathSegments[$index + 2])) {
            $relationId = (int) $pathSegments[$index + 2];
        }

        return [
            'parentIdIndex' => $index,
            'parentId' => $parentId,
            'originalRelation' => $originalRelation,
            'relationName' => $relationName,
            'relationId' => $relationId,
            'parentModelClass' => $parentModelClass,
        ];
    }

    private function resolveDeepPivotModel(array $pivotLevels): string
    {
        $modelClass = null;

        foreach ($pivotLevels as $level) {
            $modelClass = $this->getRelationModelClass(
                $level['parentModelClass'],
                $level['relationName'],
            );

            if (! $modelClass) {
                throw new NotFoundHttpException(
                    config('app.debug')
                        ? "'{$level['relationName']}' " . __('api.route.relation_not_found_debug')
                        : __('api.route.resource_not_found'),
                );
            }
        }

        return $modelClass;
    }

    private function getRelationModelClass(string $parentModelClass, string $relationName): ?string
    {
        try {
            /** @var BaseModel $parentModel */
            $parentModel = new $parentModelClass;

            if ($parentModel instanceof BaseModel
                && ! in_array($relationName, $parentModel->getAllowedRelations(), true)
            ) {
                return null;
            }

            if (! method_exists($parentModel, $relationName)) {
                return null;
            }

            $relationObject = $parentModel->{$relationName}();

            if (! $relationObject instanceof Relation) {
                return null;
            }

            return get_class($relationObject->getRelated());
        } catch (\Exception) {
            return null;
        }
    }

    private function extractTableName(string $modelClass): string
    {
        try {
            return (new $modelClass)->getTable();
        } catch (\Exception) {
            $className = class_basename($modelClass);
            $baseName = Str::before($className, 'Model');

            return Str::plural(Str::snake($baseName));
        }
    }
}
