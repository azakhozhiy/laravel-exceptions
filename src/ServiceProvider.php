<?php

declare(strict_types=1);

namespace AZakhozhiy\Laravel\Exceptions;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
    }
}
