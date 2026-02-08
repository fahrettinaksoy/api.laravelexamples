<?php

declare(strict_types=1);

namespace App\SmartQuery\Enums;

enum FilterOperator: string
{
    case EQUAL = '=';
    case NOT_EQUAL = '!=';
    case GREATER_THAN = '>';
    case LESS_THAN = '<';
    case GREATER_THAN_OR_EQUAL = '>=';
    case LESS_THAN_OR_EQUAL = '<=';
    case DYNAMIC = 'dynamic';  // User specifies operator in value

    public static function parseDynamic(string $value): array
    {
        if (preg_match('/^(>=|<=|!=|>|<|=)(.+)$/', $value, $matches)) {
            return [
                'operator' => $matches[1],
                'value' => trim($matches[2]),
            ];
        }

        return [
            'operator' => '=',
            'value' => $value,
        ];
    }
}
