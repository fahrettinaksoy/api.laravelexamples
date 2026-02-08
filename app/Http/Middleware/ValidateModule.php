<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ValidateModule
{
    private array $resolvedModels = [];

    private const CACHE_DURATION = 3600;

    private const SKIP_ROUTES = [
        'definition/location/search',
        'catalog/availability/check',
        'consumer/emerging/search',
    ];

    private const MIN_SEGMENTS = 3;

    public function handle(Request $request, Closure $next): mixed
    {
        $segments = $request->segments();

        if (count($segments) < self::MIN_SEGMENTS) {
            return $next($request);
        }

        if ($this->shouldSkipResolution($segments)) {
            return $next($request);
        }

        $resolvedModel = $this->getResolvedModel($segments);
        $this->attachModelToRequest($request, $resolvedModel);

        return $next($request);
    }

    private function shouldSkipResolution(array $segments): bool
    {
        $pathAfterApi = implode('/', array_slice($segments, 2));

        return collect(self::SKIP_ROUTES)
            ->contains(fn ($route) => str_starts_with($pathAfterApi, $route));
    }

    private function getResolvedModel(array $segments): array
    {
        $cacheKey = $this->generateCacheKey($segments);

        if (isset($this->resolvedModels[$cacheKey])) {
            return $this->resolvedModels[$cacheKey];
        }

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            $this->resolvedModels[$cacheKey] = $cached;

            return $cached;
        }

        $pathSegments = array_slice($segments, 2);
        $resolvedModel = $this->resolveModelFromPath($pathSegments);

        $this->cacheResolvedModel($cacheKey, $resolvedModel);

        return $resolvedModel;
    }

    private function generateCacheKey(array $segments): string
    {
        return 'model_resolution_'.md5(implode('/', $segments));
    }

    private function cacheResolvedModel(string $cacheKey, array $resolvedModel): void
    {
        $this->resolvedModels[$cacheKey] = $resolvedModel;
        Cache::put($cacheKey, $resolvedModel, self::CACHE_DURATION);
    }

    private function resolveModelFromPath(array $pathSegments): array
    {
        return $this->isPivotRoute($pathSegments)
            ? $this->resolvePivotRoute($pathSegments)
            : $this->resolveStandardRoute($pathSegments);
    }

    private function isPivotRoute(array $pathSegments): bool
    {
        if (count($pathSegments) < 3) {
            return false;
        }

        for ($i = 0; $i < count($pathSegments) - 1; $i++) {
            if ($this->isValidPivotPattern($pathSegments[$i], $pathSegments[$i + 1])) {
                return true;
            }
        }

        return false;
    }

    private function isValidPivotPattern(string $current, string $next): bool
    {
        return is_numeric($current)
            && ! is_numeric($next)
            && preg_match('/^[a-zA-Z_-]+$/', $next);
    }

    private function resolvePivotRoute(array $pathSegments): array
    {
        $pivotLevels = $this->extractPivotLevels($pathSegments);

        if (empty($pivotLevels)) {
            throw new InvalidArgumentException('Geçersiz pivot route yapısı');
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

        for ($i = 0; $i < count($pathSegments); $i++) {
            if (! is_numeric($pathSegments[$i])) {
                $currentPath[] = $pathSegments[$i];

                continue;
            }

            if (! isset($pathSegments[$i + 1]) || is_numeric($pathSegments[$i + 1])) {
                continue;
            }

            $pivotLevel = $this->buildPivotLevel($pathSegments, $i, $currentPath, $currentModelClass);
            $levels[] = $pivotLevel;

            $currentModelClass = $this->getRelationModelClass(
                $pivotLevel['parentModelClass'],
                $pivotLevel['relationName'],
            );

            $i++;
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

        $parentModelClass = $currentModelClass ?? $this->buildModelClass($currentPath);

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
                throw new InvalidArgumentException(
                    "'{$level['relationName']}' relation'u {$level['parentModelClass']} sınıfında tanımlı değil",
                );
            }
        }

        return $modelClass;
    }

    private function getRelationModelClass(string $parentModelClass, string $relationName): ?string
    {
        try {
            $parentModel = new $parentModelClass;

            if (! method_exists($parentModel, $relationName)) {
                return null;
            }

            $relationObject = $parentModel->{$relationName}();

            return get_class($relationObject->getRelated());

        } catch (\Exception $e) {
            return null;
        }
    }

    private function resolveStandardRoute(array $pathSegments): array
    {
        $modelPath = $this->extractModelPath($pathSegments);

        if (empty($modelPath)) {
            throw new InvalidArgumentException('Boş model path');
        }

        return [
            'isPivotRoute' => false,
            'modelClass' => $this->buildModelClass($modelPath),
            'tableName' => end($modelPath),
            'mainModelPath' => implode('/', $modelPath),
            'fullPath' => implode('/', $pathSegments),
        ];
    }

    private function extractModelPath(array $pathSegments): array
    {
        return is_numeric(end($pathSegments))
            ? array_slice($pathSegments, 0, -1)
            : $pathSegments;
    }

    private function extractTableName(string $modelClass): string
    {
        try {
            $model = new $modelClass;

            return $model->getTable();

        } catch (\Exception $e) {
            return $this->generateTableNameFromClass($modelClass);
        }
    }

    private function generateTableNameFromClass(string $modelClass): string
    {
        $className = class_basename($modelClass);
        $baseName = Str::before($className, 'Model');
        $snakeName = Str::snake($baseName);

        return Str::plural($snakeName);
    }

    private function buildModelClass(array $pathSegments, ?string $customClassName = null): string
    {
        $namespaceParts = array_map([Str::class, 'studly'], $pathSegments);
        $fullClassName = 'App\\Models\\'.implode('\\', $namespaceParts);

        return $fullClassName;
    }

    private function attachModelToRequest(Request $request, array $modelData): void
    {
        foreach ($modelData as $key => $value) {
            $request->attributes->set($key, $value);
        }
    }
}
