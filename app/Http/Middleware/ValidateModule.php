<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\RouteResolver\PivotRouteResolver;
use App\Support\RouteResolver\StandardRouteResolver;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ValidateModule
{
    private array $resolvedModels = [];

    private const MIN_SEGMENTS = 3;

    public function __construct(
        private readonly StandardRouteResolver $standardResolver,
        private readonly PivotRouteResolver $pivotResolver,
    ) {}

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

        $request->route()->forgetParameter('path');

        return $next($request);
    }

    private function shouldSkipResolution(array $segments): bool
    {
        $skipRoutes = array_filter(config('modules.skip_routes', []));

        if (empty($skipRoutes)) {
            return false;
        }

        $pathAfterApi = implode('/', array_slice($segments, 2));

        return collect($skipRoutes)
            ->contains(fn (string $route) => str_starts_with($pathAfterApi, $route));
    }

    private function getResolvedModel(array $segments): array
    {
        $cacheKey = $this->generateCacheKey($segments);

        if (isset($this->resolvedModels[$cacheKey])) {
            return $this->resolvedModels[$cacheKey];
        }

        $cached = Cache::get($cacheKey);

        if ($cached !== null) {
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
        $pathOnly = array_filter($segments, fn (string $s) => ! is_numeric($s));

        return 'model_resolution:' . implode('/', $pathOnly);
    }

    private function cacheResolvedModel(string $cacheKey, array $resolvedModel): void
    {
        $cacheTtl = (int) config('cache.repository_ttl', 3600);

        $this->resolvedModels[$cacheKey] = $resolvedModel;
        Cache::put($cacheKey, $resolvedModel, $cacheTtl);
    }

    private function resolveModelFromPath(array $pathSegments): array
    {
        return PivotRouteResolver::isPivotRoute($pathSegments)
            ? $this->pivotResolver->resolve($pathSegments)
            : $this->standardResolver->resolve($pathSegments);
    }

    private function attachModelToRequest(Request $request, array $modelData): void
    {
        foreach ($modelData as $key => $value) {
            $request->attributes->set($key, $value);
        }

        $this->injectParentScope($request, $modelData);
    }

    private function injectParentScope(Request $request, array $modelData): void
    {
        if (empty($modelData['isPivotRoute'])) {
            return;
        }

        $parentId = $modelData['parentId'] ?? null;
        $parentModelClass = $modelData['parentModelClass'] ?? null;

        if (! $parentId || ! $parentModelClass) {
            return;
        }

        $parentName = Str::snake(
            Str::before(class_basename($parentModelClass), 'Model'),
        );

        $request->merge([$parentName . '_id' => $parentId]);
    }
}
