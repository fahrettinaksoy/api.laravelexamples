<?php

declare(strict_types=1);

namespace App\Attributes\Model;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class FormField
{
    public function __construct(
        public string $type,
        public mixed $default = null,
        public array $relationship = [],
        public array $options = [],
        public int $sort_order = 0,
    ) {}
}
