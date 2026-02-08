<?php

declare(strict_types=1);

namespace App\SmartQuery\Support;

use Illuminate\Support\Collection;

class DTOMapper
{
    public static function map(\stdClass $result, string $dtoClass): mixed
    {
        if (method_exists($dtoClass, 'fromStdClass')) {
            return $dtoClass::fromStdClass($result);
        }

        if (method_exists($dtoClass, 'fromArray')) {
            return $dtoClass::fromArray((array) $result);
        }

        return new $dtoClass(...(array) $result);
    }

    public static function mapCollection(Collection $results, string $dtoClass): Collection
    {
        return $results->map(fn ($result) => static::map($result, $dtoClass));
    }
}
