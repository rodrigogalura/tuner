<?php

namespace Tests;

// use PHPUnit\Framework\TestCase as BaseTestCase;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Validation\ValidationServiceProvider;

abstract class UnitTestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ValidationServiceProvider::class,
        ];
    }
}
