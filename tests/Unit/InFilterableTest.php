<?php

use RGalura\ApiIgniter\InFilterable;

test('inFilter', function (array|string $filterableFields, array $clientIn, array $expect): void {
    // Prepare
    $class = new class
    {
        use InFilterable;
    };

    $method = new ReflectionMethod($class, 'inFilter');
    $method->setAccessible(true);

    $_GET['in'] = $clientIn;

    // Act & Assert
    expect($method->invoke(null, $filterableFields))->toBe($expect);
})
    ->with([
        [['*'], [''], []],
        [
            '*',
            ['name' => 'bar'],
            [
                ['AND', 'name', false, ['bar']],
            ],
        ],
        [
            ['name'],
            ['OR! name' => 'bar,baz', 'AND mname' => 'doe'],
            [
                ['OR', 'name', true, ['bar', 'baz']],
            ],
        ],
        [
            'name, lname',
            ['name' => 'bar,baz', 'mname' => 'doe'],
            [
                ['AND', 'name', false, ['bar', 'baz']],
            ],
        ],
    ]);
