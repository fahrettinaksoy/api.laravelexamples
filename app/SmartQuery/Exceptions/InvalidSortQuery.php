<?php

declare(strict_types=1);

namespace App\SmartQuery\Exceptions;

use Exception;

class InvalidSortQuery extends Exception
{
    public static function sortNotAllowed(string $sort, array $allowedSorts): self
    {
        $allowedSortsString = implode(', ', $allowedSorts);

        return new static(
            "Sort '{$sort}' is not allowed. Allowed sorts: {$allowedSortsString}",
        );
    }

    public static function sortsNotAllowed(array $sorts, array $allowedSorts): self
    {
        $sortsString = implode(', ', $sorts);
        $allowedSortsString = implode(', ', $allowedSorts);

        return new static(
            "Sorts '{$sortsString}' are not allowed. Allowed sorts: {$allowedSortsString}",
        );
    }
}
