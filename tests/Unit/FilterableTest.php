<?php

use RGalura\ApiIgniter\Filterable;

test('filter', function (array|string $filterableFields, array $clientFilter, array $expect): void {
    // Prepare
    $class = new class
    {
        use Filterable;
    };

    $method = new ReflectionMethod($class, 'filter');
    $method->setAccessible(true);

    $_GET['filter'] = $clientFilter;

    // Act & Assert
    expect($method->invoke(null, $filterableFields))->toBe($expect);
})
    ->with([
        [[], [], []],
        [
            ['*'],
            ['foo' => 'bar', 'AND! john' => 'doe baz'],
            [
                ['AND', 'foo', false, '=', 'bar'],
                ['AND', 'john', true, '=', 'doe baz'],
            ],
        ],
        [
            ['foo'],
            ['or! foo' => 'bar', 'john' => 'doe'],
            [['OR', 'foo', true, '=', 'bar']],
        ],
        [
            'foo, john',
            ['and! foo' => 'bar', 'OR! john' => 'doe'],
            [
                ['AND', 'foo', true, '=', 'bar'],
                ['OR', 'john', true, '=', 'doe'],
            ],
        ],
    ]);
