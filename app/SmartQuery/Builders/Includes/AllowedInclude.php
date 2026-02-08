<?php

declare(strict_types=1);

namespace App\SmartQuery\Builders\Includes;

use Closure;

class AllowedInclude
{
    protected string $name;

    protected ?string $internalName;

    protected IncludeInterface $includeClass;

    public function __construct(
        string $name,
        IncludeInterface $includeClass,
        ?string $internalName = null
    ) {
        $this->name = $name;
        $this->includeClass = $includeClass;
        $this->internalName = $internalName ?? $name;
    }

    public static function relationship(string $name, ?string $internalName = null): static
    {
        return new static(
            $name,
            new RelationshipInclude,
            $internalName
        );
    }

    public static function count(string $name, ?string $internalName = null): static
    {
        $internal = $internalName ?? preg_replace('/Count$/', '', $name);

        return new static(
            $name,
            new CountInclude,
            $internal
        );
    }

    public static function exists(string $name, ?string $internalName = null): static
    {
        $internal = $internalName ?? preg_replace('/Exists$/', '', $name);

        return new static(
            $name,
            new ExistsInclude,
            $internal
        );
    }

    public static function custom(
        string $name,
        IncludeInterface $include,
        ?string $internalName = null
    ): static {
        return new static(
            $name,
            new CustomInclude($include),
            $internalName
        );
    }

    public static function callback(
        string $name,
        Closure $callback,
        ?string $internalName = null
    ): static {
        return new static(
            $name,
            new CallbackInclude($callback),
            $internalName
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getInternalName(): string
    {
        return $this->internalName;
    }

    public function getIncludeClass(): IncludeInterface
    {
        return $this->includeClass;
    }
}
