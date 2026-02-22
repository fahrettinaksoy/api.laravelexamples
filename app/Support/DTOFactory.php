<?php

declare(strict_types=1);

namespace App\Support;

use App\DataTransferObjects\BaseDTO;
use Illuminate\Http\Request;

class DTOFactory
{
    public static function fromRequest(string $dtoClass, Request $request, string $action = 'store'): BaseDTO
    {
        $metadata = MetadataResolver::resolve($dtoClass);
        $args = [];

        foreach ($metadata as $field) {
            if (! in_array($action, $field['actions'], true)) {
                continue;
            }

            $value = $request->validated($field['name']);
            $args[$field['name']] = self::castValue($value, $field['phpType']);
        }

        return new $dtoClass(...$args);
    }

    private static function castValue(mixed $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'int' => (int) $value,
            'float' => (float) $value,
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'string' => (string) $value,
            'array' => (array) $value,
            default => $value,
        };
    }
}
