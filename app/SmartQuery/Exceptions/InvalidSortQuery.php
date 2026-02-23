<?php

declare(strict_types=1);

namespace App\SmartQuery\Exceptions;

use Exception;

class InvalidSortQuery extends Exception
{
    public static function sortNotAllowed(string $sort, array $allowedSorts): self
    {
        return new static(
            __('api.smartquery.sort_not_allowed', [
                'sort' => $sort,
                'allowed' => implode(', ', $allowedSorts),
            ]),
        );
    }

    public static function sortsNotAllowed(array $sorts, array $allowedSorts): self
    {
        return new static(
            __('api.smartquery.sorts_not_allowed', [
                'sorts' => implode(', ', $sorts),
                'allowed' => implode(', ', $allowedSorts),
            ]),
        );
    }
}
