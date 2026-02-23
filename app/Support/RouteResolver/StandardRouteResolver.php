<?php

declare(strict_types=1);

namespace App\Support\RouteResolver;

use App\Models\BaseModel;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StandardRouteResolver
{
    public function resolve(array $pathSegments): array
    {
        $modelPath = $this->extractModelPath($pathSegments);

        if (empty($modelPath)) {
            throw new NotFoundHttpException(__('api.route.invalid_path'));
        }

        $modelClass = $this->buildModelClass($modelPath);

        return [
            'isPivotRoute' => false,
            'modelClass' => $modelClass,
            'tableName' => end($modelPath),
            'mainModelPath' => implode('/', $modelPath),
            'fullPath' => implode('/', $pathSegments),
        ];
    }

    public function buildModelClass(array $pathSegments): string
    {
        foreach ($pathSegments as $segment) {
            if (! preg_match('/^[a-zA-Z][a-zA-Z0-9_-]*$/', $segment)) {
                throw new NotFoundHttpException(__('api.route.invalid_segment'));
            }
        }

        $namespaceParts = array_map([Str::class, 'studly'], $pathSegments);
        $modelName = end($namespaceParts);
        $modelClass = 'App\\Models\\' . implode('\\', $namespaceParts) . '\\' . $modelName . 'Model';

        if (! class_exists($modelClass) || ! is_subclass_of($modelClass, BaseModel::class)) {
            throw new NotFoundHttpException(
                config('app.debug')
                    ? "'{$modelClass}' " . __('api.route.model_not_found_debug')
                    : __('api.route.resource_not_found'),
            );
        }

        return $modelClass;
    }

    private function extractModelPath(array $pathSegments): array
    {
        return is_numeric(end($pathSegments))
            ? array_slice($pathSegments, 0, -1)
            : $pathSegments;
    }
}
