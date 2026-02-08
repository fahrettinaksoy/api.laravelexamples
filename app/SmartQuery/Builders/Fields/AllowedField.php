<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Fields;

class AllowedField
{
    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
