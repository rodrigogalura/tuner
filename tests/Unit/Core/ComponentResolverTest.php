<?php

use RGalura\ApiIgniter\Services\ComponentResolver as Core;

test('bind_and_resolve', function (string $key, callable $component, mixed $expect) {
    // Prepare
    Core::bind($key, $component);

    // Act & Assert
    expect(Core::resolve($key))->toBe($expect);
})
    ->with([
        'num' => ['test', fn () => 1, 1],
        'str' => ['test', fn () => 'hello world', 'hello world'],
        'array' => ['test', fn () => [1, 'hello world'], [1, 'hello world']],
    ]);
