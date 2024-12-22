<?php

declare(strict_types=1);

namespace AZakhozhiy\Laravel\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Throwable;

class BaseServiceException extends Exception implements Arrayable
{
    public int $httpCode = 500;
    private GenericExceptionCategory $exceptionCategory;
    private GenericExceptionObject $exceptionObject;

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getExceptionCategory(): GenericExceptionCategory
    {
        return $this->exceptionCategory;
    }

    public function getExceptionObject(): GenericExceptionObject
    {
        return $this->exceptionObject;
    }

    public static function build(
        GenericExceptionCategory $cat,
        GenericExceptionObject   $excObj,
        ?Throwable               $previous = null
    ): static {
        $errorMsg = is_array($excObj->getErrorMessage()) ? '' : $excObj->getErrorMessage();
        if (empty($errorMsg) && $previous) {
            $errorMsg = $previous->getMessage();
        }

        $obj = new static($errorMsg, $excObj->getErrorCode(), $previous);

        $obj->exceptionCategory = $cat;
        $obj->exceptionObject = $excObj;

        return $obj;
    }

    public function getHttpCode(): int
    {
        return $this->exceptionObject->getHttpCode();
    }

    public function toArray()
    {
        return [
            'error_code' => $this->exceptionCategory->getSlug() . '-' . $this->exceptionObject->getErrorCode(),
            'error_name' => $this->exceptionObject->getErrorName(),
            'error_desc' => $this->exceptionObject->getErrorDesc(),
            'message' => $this->exceptionObject->getErrorMessage()
        ];
    }
}
