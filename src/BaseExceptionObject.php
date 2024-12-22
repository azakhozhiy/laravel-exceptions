<?php

declare(strict_types=1);

namespace AZakhozhiy\Laravel\Exceptions;

abstract class BaseExceptionObject
{
    protected int $errorCode;
    protected string $errorName;
    protected ?string $errorDesc = null;
    protected string|array $errorMessage;
    protected int $httpCode;

    public function __construct()
    {
        $this->errorCode = static::getErrorCode();
        $this->errorName = static::getErrorName();
        $this->errorDesc = static::getErrorDesc();
        $this->errorMessage = static::getErrorMessage();
        $this->httpCode = static::getHttpCode();
    }

    abstract public static function getErrorCode(): int;

    abstract public static function getErrorName(): string;

    abstract public static function getErrorDesc(): ?string;

    abstract public static function getErrorMessage(): string|array;

    abstract public static function getHttpCode(): int;
}
