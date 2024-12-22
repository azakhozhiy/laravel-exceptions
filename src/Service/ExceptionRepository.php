<?php

declare(strict_types=1);

namespace AZakhozhiy\Laravel\Exceptions\Service;

use AZakhozhiy\Laravel\Exceptions\Contract\ExceptionRepositoryInterface;
use Closure;
use Throwable;
use AZakhozhiy\Laravel\Exceptions\BaseExceptionCategory;
use AZakhozhiy\Laravel\Exceptions\BaseExceptionObject;
use AZakhozhiy\Laravel\Exceptions\BaseServiceException;
use AZakhozhiy\Laravel\Exceptions\GenericExceptionCategory;
use AZakhozhiy\Laravel\Exceptions\GenericExceptionObject;
use AZakhozhiy\Laravel\Exceptions\Mapping\ExceptionCategoryItem;
use AZakhozhiy\Laravel\Exceptions\System\ExceptionCategoryAlreadyRegistered;
use AZakhozhiy\Laravel\Exceptions\System\ExceptionCodeAlreadyRegistered;
use AZakhozhiy\Laravel\Exceptions\System\UnknownExceptionCategory;
use AZakhozhiy\Laravel\Exceptions\System\UnknownExceptionCode;

class ExceptionRepository implements ExceptionRepositoryInterface
{
    /** @var Closure[] */
    protected array $categories;

    /** @var Closure[] */
    protected array $exceptions;

    public function getCategoriesSlugs(): array
    {
        return array_keys($this->categories);
    }

    /**
     * @param class-string<BaseExceptionCategory> $catClass
     * @return $this
     */
    public function registerExceptionCategory(string $catClass): static
    {
        $this->assertCategoryNotExists($catClass::getSlug());

        $this->categories[$catClass::getSlug()]
            = static fn () => ExceptionCategoryItem::createFromCategory($catClass);

        return $this;
    }

    /**
     * @param class-string<BaseExceptionCategory> $catClass
     * @param class-string<BaseExceptionObject> $exceptionClass
     *
     * @return $this
     */
    public function registerException(string $catClass, string $exceptionClass): static
    {
        if (!isset($this->categories[$catClass::getSlug()])) {
            $this->registerExceptionCategory($catClass);
        }

        $this->assertCategoryAndExceptionNotExist($catClass::getSlug(), $exceptionClass::getErrorCode());

        $this->exceptions[$catClass::getSlug()][$exceptionClass::getErrorCode()] =
            static fn () => new $exceptionClass();

        return $this;
    }

    /**
     * @param string $catSlug
     * @return BaseExceptionObject[]
     */
    public function getExceptionObjectsByCategory(string $catSlug): array
    {
        $this->assertCategoryExists($catSlug);

        $items = [];
        foreach ($this->exceptions[$catSlug] as $exceptionFn) {
            $items[] = $exceptionFn();
        }

        return $items;
    }

    public function buildException(
        string            $catSlug,
        int               $errorCode,
        string|array|null $customMessage = null,
        ?int              $customHttpCode = null,
        ?Throwable        $previous = null
    ): BaseServiceException {
        $this->assertCategoryExists($catSlug);
        $categoryItem = $this->getExceptionCategory($catSlug);

        $this->assertCategoryAndExceptionExist($catSlug, $errorCode);
        $exceptionObj = $this->getExceptionObject($catSlug, $errorCode);

        $errorMsg = $customMessage ?: $exceptionObj::getErrorMessage();
        $errorHttpCode = $customHttpCode ?: $exceptionObj::getHttpCode();

        return BaseServiceException::build(
            (new GenericExceptionCategory(
                $categoryItem->getName(),
                $categoryItem->getSlug()
            )),
            (new GenericExceptionObject(
                $exceptionObj::getErrorCode(),
                $exceptionObj::getErrorName(),
                $errorMsg,
                $errorHttpCode,
                $exceptionObj::getErrorDesc()
            )),
            $previous
        );
    }

    public function getExceptionObject(string $catSlug, int $errorCode): BaseExceptionObject
    {
        $this->assertCategoryExists($catSlug);
        $this->assertCategoryAndExceptionExist($catSlug, $errorCode);

        return $this->exceptions[$catSlug][$errorCode]();
    }

    public function getExceptionCategory(string $catSlug, bool $withCodes = false): ExceptionCategoryItem
    {
        $this->assertCategoryExists($catSlug);

        /** @var ExceptionCategoryItem $cat */
        $cat = $this->categories[$catSlug]();

        if ($withCodes) {
            $cat->addCodes($this->getExceptionObjectsByCategory($catSlug));
        }

        return $cat;
    }

    protected function assertCategoryNotExists(string $catSlug): void
    {
        if (isset($this->categories[$catSlug])) {
            throw new ExceptionCategoryAlreadyRegistered(
                "Category $catSlug already registered."
            );
        }
    }

    protected function assertCategoryExists(string $catSlug): void
    {
        if (!isset($this->categories[$catSlug])) {
            throw new UnknownExceptionCategory(
                "Unknown exception category $catSlug."
            );
        }
    }

    protected function assertCategoryAndExceptionNotExist(string $catSlug, int $errorCode): void
    {
        if (isset($this->exceptions[$catSlug][$errorCode])) {
            throw new ExceptionCodeAlreadyRegistered(
                "Exception code [$errorCode] already " .
                "registered for the [$catSlug] category."
            );
        }
    }

    protected function assertCategoryAndExceptionExist(string $catSlug, int $errorCode): void
    {
        if (!isset($this->exceptions[$catSlug][$errorCode])) {
            throw new UnknownExceptionCode(
                "Unknown exception code $errorCode for category $catSlug."
            );
        }
    }
}
