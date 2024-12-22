<?php

namespace AZakhozhiy\Laravel\Exceptions\Contract;

use AZakhozhiy\Laravel\Exceptions\BaseExceptionObject;
use AZakhozhiy\Laravel\Exceptions\BaseServiceException;
use AZakhozhiy\Laravel\Exceptions\Mapping\ExceptionCategoryItem;
use Throwable;

interface ExceptionRepositoryInterface
{
    public function getCategoriesSlugs(): array;

    public function registerExceptionCategory(string $catClass): static;

    public function registerException(string $catClass, string $exceptionClass): static;

    public function getExceptionObjectsByCategory(string $catSlug): array;

    public function getExceptionObject(string $catSlug, int $errorCode): BaseExceptionObject;

    public function getExceptionCategory(string $catSlug, bool $withCodes = false): ExceptionCategoryItem;

    public function buildException(
        string            $catSlug,
        int               $errorCode,
        string|array|null $customMessage = null,
        ?int              $customHttpCode = null,
        ?Throwable        $previous = null
    ): BaseServiceException;
}