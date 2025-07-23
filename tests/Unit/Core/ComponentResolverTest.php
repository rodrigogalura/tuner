<?php

use RGalura\ApiIgniter\Services\ComponentResolver as Core;

test('bind_and_resolve', function (string $key, callable $component, mixed $expect): void {
    // Prepare
    Core::bind($key, $component);

    // Act & Assert
    expect(Core::resolve($key))->toBe($expect);
})
    ->with([
        'num' => ['test', fn (): int => 1, 1],
        'str' => ['test', fn (): string => 'hello world', 'hello world'],
        'array' => ['test', fn (): array => [1, 'hello world'], [1, 'hello world']],
    ]);
