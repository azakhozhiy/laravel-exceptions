<?php

declare(strict_types=1);

namespace AZakhozhiy\Laravel\Exceptions;

abstract class BaseExceptionCategory
{
    protected string $name;
    protected string $slug;

    public function __construct()
    {
        $this->name = static::getName();
        $this->slug = static::getSlug();
    }

    abstract public static function getName(): string;

    abstract public static function getSlug(): string;
}
