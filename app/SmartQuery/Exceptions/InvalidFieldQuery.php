<?php

declare(strict_types=1);

namespace App\SmartQuery\Exceptions;

use Exception;

class InvalidFieldQuery extends Exception
{
    public static function fieldNotAllowed(string $field, array $allowedFields): self
    {
        return new static(
            __('api.smartquery.field_not_allowed', [
                'field' => $field,
                'allowed' => implode(', ', $allowedFields),
            ]),
        );
    }

    public static function fieldsNotAllowed(array $fields, array $allowedFields): self
    {
        return new static(
            __('api.smartquery.fields_not_allowed', [
                'fields' => implode(', ', $fields),
                'allowed' => implode(', ', $allowedFields),
            ]),
        );
    }
}
