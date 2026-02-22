<?php

declare(strict_types=1);

namespace App\Attributes\Model;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class TableColumn
{
    public const DEFAULT_ACTIONS = [];

    public function __construct(
        public array $actions = self::DEFAULT_ACTIONS,
        public array $sorting = [],
        public string $primaryKey = '',
    ) {}
}
