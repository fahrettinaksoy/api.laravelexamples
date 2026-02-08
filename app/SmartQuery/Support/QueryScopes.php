<?php

declare(strict_types=1);

namespace App\SmartQuery\Support;

class QueryScopes
{
    protected static array $scopes = [];

    public static function register(string $table, string $name, callable $callback): void
    {
        static::$scopes[$table][$name] = $callback;
    }

    public static function has(string $table, string $name): bool
    {
        return isset(static::$scopes[$table][$name]);
    }

    public static function get(string $table, string $name): ?callable
    {
        return static::$scopes[$table][$name] ?? null;
    }

    public static function apply($query, string $table, string $name, $value): void
    {
        if ($scope = static::get($table, $name)) {
            call_user_func($scope, $query, $value);
        }
    }

    public static function forTable(string $table): array
    {
        return static::$scopes[$table] ?? [];
    }

    public static function clear(): void
    {
        static::$scopes = [];
    }

    public static function clearTable(string $table): void
    {
        unset(static::$scopes[$table]);
    }
}
