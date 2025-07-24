<?php

namespace Tests;

// use PHPUnit\Framework\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class FeatureTestCase extends BaseTestCase
{
    use RefreshDatabase;
    use WithWorkbench;

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('tweaker', [
            'projection' => [
                'intersect_key' => 'fields',
                'except_key' => 'fields!',
            ],

            'search' => [
                'key' => 'search',
                'minimum_length' => 2,
            ],

            'sort' => [
                'key' => 'sort',
            ],
        ]);
    }
}
