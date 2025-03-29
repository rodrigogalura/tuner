<?php

namespace Tests;

// use PHPUnit\Framework\TestCase as BaseTestCase;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use WithWorkbench;
    use RefreshDatabase;
}
