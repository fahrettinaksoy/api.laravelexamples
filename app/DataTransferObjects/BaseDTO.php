<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

abstract class BaseDTO
{
    public static function fromArray(array $data): static
    {
        return new static(...$data);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function only(): array
    {
        return array_filter(
            $this->toArray(),
            static fn (mixed $value): bool => $value !== null,
        );
    }
}
