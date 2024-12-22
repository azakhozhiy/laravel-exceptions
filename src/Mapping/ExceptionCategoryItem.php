<?php

declare(strict_types=1);

namespace AZakhozhiy\Laravel\Exceptions\Mapping;

use AZakhozhiy\Laravel\Exceptions\BaseExceptionCategory;
use AZakhozhiy\Laravel\Exceptions\BaseExceptionObject;

class ExceptionCategoryItem
{
    protected string $slug;
    protected string $name;

    /** @var BaseExceptionObject[] */
    protected array $codes = [];

    /**
     * @param class-string<BaseExceptionCategory> $catClass
     * @return static
     */
    public static function createFromCategory(string $catClass): static
    {
        $obj = new static();

        $obj->slug = $catClass::getSlug();
        $obj->name = $catClass::getName();
        $obj->codes = [];

        return $obj;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addCodes(array $codes): static
    {
        $this->codes = array_merge($this->codes, $codes);

        return $this;
    }

    public function addCode(BaseExceptionObject $obj): static
    {
        $this->codes[$obj::getErrorCode()] = $obj;

        return $this;
    }

    public function getCode(int $code): BaseExceptionObject
    {
        return $this->codes[$code];
    }

    public function codeIsExist(int $code): bool
    {
        return isset($this->codes[$code]);
    }

    public function getCodes(): array
    {
        return $this->codes;
    }
}
