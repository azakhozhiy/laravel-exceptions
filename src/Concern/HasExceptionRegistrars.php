<?php

namespace AZakhozhiy\Laravel\Exceptions\Concern;

use Illuminate\Support\ServiceProvider;
use AZakhozhiy\Laravel\Exceptions\Contract\RegisterExceptionsInterface;
use AZakhozhiy\Laravel\Exceptions\Service\ExceptionRepository;

/**
 * @mixin ServiceProvider
 */
trait HasExceptionRegistrars
{

    /**
     * @return class-string<RegisterExceptionsInterface>[]
     */
    public function getCustomExceptionRegistrars(): array
    {
        return [];
    }

    public function extendExceptionRepository(callable $extendFn): void
    {
        $this->app->extend(
            ExceptionRepository::class,
            function (ExceptionRepository $repo) use ($extendFn) {
                return $extendFn($repo);
            }
        );
    }

    public function registerCustomExceptionsRegistrars(): void
    {
        $registration = function (ExceptionRepository $repo) {
            /** @var class-string<RegisterExceptionsInterface> $registrarClass */
            foreach ($this->getCustomExceptionRegistrars() as $registrarClass) {
                $repo = $registrarClass::register($repo);
            }

            return $repo;
        };

        $this->extendExceptionRepository($registration);
    }
}