<?php

use RGalura\ApiIgniter\BetweenFilterable;

test('betweenFilter', function (array|string $filterableFields, array $clientFilter, array $expect) {
    // Prepare
    $class = new class
    {
        use BetweenFilterable;
    };

    $method = new ReflectionMethod($class, 'betweenFilter');
    $method->setAccessible(true);

    $_GET['between'] = $clientFilter;

    // Act & Assert
    expect($method->invoke(null, $filterableFields))->toBe($expect);
})
    ->with([
        [[], [], []],
        ['', [], []],
        [
            ['*'],
            ['foo' => 'barbaz', 'AND! john' => 'doe, foo, bar'],
            [],
        ],
        [
            '*',
            ['foo' => 'bar,baz', 'AND! john' => 'doe,   foo'],
            [
                ['AND', 'foo', false, ['bar', 'baz']],
                ['AND', 'john', true, ['doe', 'foo']],
            ],
        ],
        [
            ['foo'],
            ['OR! foo' => 'bar , foe', 'john' => 'doe'],
            [['OR', 'foo', true, ['bar', 'foe']]],
        ],
        [
            'foo, john',
            ['AND! foo' => 'hello   ,   world', 'OR john' => 'doe'],
            [
                ['AND', 'foo', true, ['hello', 'world']],
            ],
        ],
    ]);
