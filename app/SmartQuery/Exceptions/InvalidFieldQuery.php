<?php

declare(strict_types=1);

namespace App\SmartQuery\Exceptions;

use Exception;

class InvalidFieldQuery extends Exception
{
    public static function fieldNotAllowed(string $field, array $allowedFields): self
    {
        $allowedFieldsString = implode(', ', $allowedFields);

        return new static(
            "Field '{$field}' is not allowed. Allowed fields: {$allowedFieldsString}",
        );
    }

    public static function fieldsNotAllowed(array $fields, array $allowedFields): self
    {
        $fieldsString = implode(', ', $fields);
        $allowedFieldsString = implode(', ', $allowedFields);

        return new static(
            "Fields '{$fieldsString}' are not allowed. Allowed fields: {$allowedFieldsString}",
        );
    }
}
