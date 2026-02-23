<?php

declare(strict_types=1);

namespace App\SmartQuery\Exceptions;

use Exception;

class InvalidIncludeQuery extends Exception
{
    public static function includeNotAllowed(string $include, array $allowedIncludes): self
    {
        return new static(
            __('api.smartquery.include_not_allowed', [
                'include' => $include,
                'allowed' => implode(', ', $allowedIncludes),
            ]),
        );
    }

    public static function includesNotAllowed(array $includes, array $allowedIncludes): self
    {
        return new static(
            __('api.smartquery.includes_not_allowed', [
                'includes' => implode(', ', $includes),
                'allowed' => implode(', ', $allowedIncludes),
            ]),
        );
    }
}
