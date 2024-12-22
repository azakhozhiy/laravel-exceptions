<?php

declare(strict_types=1);

namespace AZakhozhiy\Laravel\Package\Tests;

use AZakhozhiy\Laravel\Package\ServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }
}
