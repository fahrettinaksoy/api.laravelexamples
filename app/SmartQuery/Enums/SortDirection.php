<?php

declare(strict_types=1);

namespace App\SmartQuery\Enums;

enum SortDirection: string
{
    case ASCENDING = 'asc';
    case DESCENDING = 'desc';

    public function opposite(): self
    {
        return match ($this) {
            self::ASCENDING => self::DESCENDING,
            self::DESCENDING => self::ASCENDING,
        };
    }

    public function isDescending(): bool
    {
        return $this === self::DESCENDING;
    }

    public function isAscending(): bool
    {
        return $this === self::ASCENDING;
    }
}
