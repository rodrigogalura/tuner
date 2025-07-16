<?php

namespace Tests;

// use PHPUnit\Framework\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    use WithWorkbench;

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('tweaker', [
            'projection' => [
                'include_key' => 'fields',
                'exclude_key' => 'fields!',
            ],

            'searching' => [
                'key' => 'search',
                'minimum_length' => 2,
            ],
        ]);
    }
}
