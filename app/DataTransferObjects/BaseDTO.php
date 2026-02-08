<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Http\Request;

abstract class BaseDTO
{
    abstract public static function fromRequest(Request $request): static;

    abstract public function toArray(): array;

    public static function fromArray(array $data): static
    {
        return new static(...$data);
    }

    public function only(): array
    {
        return array_filter($this->toArray(), function ($value) {
            return $value !== null;
        });
    }

    public function merge(self|array $data): static
    {
        $mergedData = array_merge(
            $this->toArray(),
            $data instanceof self ? $data->toArray() : $data
        );

        return static::fromArray($mergedData);
    }
}
