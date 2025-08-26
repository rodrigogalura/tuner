<?php

namespace Tests;

// use PHPUnit\Framework\TestCase as BaseTestCase;
use Illuminate\Validation\ValidationServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class UnitTestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ValidationServiceProvider::class,
        ];
    }
}
