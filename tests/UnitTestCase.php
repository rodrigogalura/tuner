<?php

namespace Tests;

// use PHPUnit\Framework\TestCase as BaseTestCase;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class UnitTestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Illuminate\Validation\ValidationServiceProvider::class,
        ];
    }
}
