<?php

declare(strict_types=1);

namespace AZakhozhiy\Laravel\Exceptions;

class GenericExceptionObject
{
    public function __construct(
        protected int $errorCode,
        protected string $errorName,
        protected array|string $errorMessage,
        protected int $httpCode,
        protected ?string $errorDesc = null
    ) {
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    public function getErrorName(): string
    {
        return $this->errorName;
    }

    public function getErrorDesc(): ?string
    {
        return $this->errorDesc;
    }

    public function getErrorMessage(): array|string
    {
        return $this->errorMessage;
    }
}
