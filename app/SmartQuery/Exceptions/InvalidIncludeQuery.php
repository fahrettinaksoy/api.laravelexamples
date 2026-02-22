<?php

declare(strict_types=1);

namespace App\SmartQuery\Exceptions;

use Exception;

class InvalidIncludeQuery extends Exception
{
    public static function includeNotAllowed(string $include, array $allowedIncludes): self
    {
        $allowedIncludesString = implode(', ', $allowedIncludes);

        return new static(
            "Include '{$include}' is not allowed. Allowed includes: {$allowedIncludesString}",
        );
    }

    public static function includesNotAllowed(array $includes, array $allowedIncludes): self
    {
        $includesString = implode(', ', $includes);
        $allowedIncludesString = implode(', ', $allowedIncludes);

        return new static(
            "Includes '{$includesString}' are not allowed. Allowed includes: {$allowedIncludesString}",
        );
    }
}
