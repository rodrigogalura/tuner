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
        $app['config']->set('tuner', [
            'projection' => [
                'key' => [
                    'intersect' => 'columns',
                    'except' => 'columns!',
                ],
            ],

            'sort' => [
                'key' => 'sort',
            ],

            'search' => [
                'key' => 'search',
                'minimum_length' => 2,
            ],

            'filter' => [
                'key' => array_combine($keys = ['filter', 'in', 'between'], $keys),
            ],

            'limit' => [
                'key' => array_combine($keys = ['limit', 'offset'], $keys),
            ],

            'pagination' => [
                'key' => 'page-size',
            ],
        ]);
    }
}
