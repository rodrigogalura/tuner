<?php

use RGalura\ApiIgniter\Traits\Projectable;

beforeEach(function () {
    $_GET = [];
});

test('fields', function (array $projectable, string $clientFields, array $expect) {
    // Prepare
    $class = new class
    {
        use Projectable;
    };

    $method = new ReflectionMethod($class, 'fields');
    $method->setAccessible(true);

    $_GET['fields'] = $clientFields;

    // Act & Assert
    expect($method->invoke(null, $projectable))->toBe($expect);
})
    ->with([
        'all' => [['fields' => '*'], 'foo', ['foo']],
        'one' => [['fields' => 'bar'], '*', ['bar']],
    ]);

test('fields!', function (array $projectable, string $clientExceptFields, array $expect) {
    // Prepare
    $class = new class
    {
        use Projectable;
    };

    $method = new ReflectionMethod($class, 'fields');
    $method->setAccessible(true);

    $_GET['fields!'] = $clientExceptFields;

    // Act & Assert
    expect($method->invoke(null, $projectable))->toBe($expect);
})
    ->with([
        [
            ['fields' => '*', 'columnListing' => ['id', 'name', 'foo']],
            'foo',
            ['id', 'name'],
        ],
    ]);

test('fields! fields', function (array $projectable, string $clientFields, string $clientExceptFields, array $expect) {
    // Prepare
    $class = new class
    {
        use Projectable;
    };

    $method = new ReflectionMethod($class, 'fields');
    $method->setAccessible(true);

    $_GET['fields'] = $clientFields;
    $_GET['fields!'] = $clientExceptFields;

    // Act & Assert
    expect($method->invoke(null, $projectable))->toBe($expect);
})
    ->with([
        [
            ['fields' => '*', 'columnListing' => ['id', 'name', 'foo']],
            'id, foo',
            'foo',
            ['id'],
        ],
    ]);

test('fields invalid', function () {
    $class = new class
    {
        use Projectable;
    };

    $method = new ReflectionMethod($class, 'fields');
    $method->setAccessible(true);

    $_GET['fields'] = 'foo';

    expect($method->invoke(null, ''))->toBe([]);
});
