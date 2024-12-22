<?php

declare(strict_types=1);

namespace AZakhozhiy\Laravel\Exceptions\Contract;

use AZakhozhiy\Laravel\Exceptions\Service\ExceptionRepository;

interface RegisterExceptionsInterface
{
    public static function register(ExceptionRepository $repo): ExceptionRepository;
}
