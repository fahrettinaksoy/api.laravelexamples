<?php

declare(strict_types=1);

namespace App\Attributes\Model;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class ActionType
{
    public const DEFAULT_ACTIONS = ['index', 'show', 'store', 'update', 'destroy'];

    public function __construct(
        public array $actions = self::DEFAULT_ACTIONS,
    ) {}
}
