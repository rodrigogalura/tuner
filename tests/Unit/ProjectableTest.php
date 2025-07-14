<?php

use RGalura\ApiIgniter\Projectable;

beforeEach(function (): void {
    $_GET = [];
});

test('fields', function (array $projectable, string $clientFields, array $expect): void {
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

test('fields!', function (array $projectable, string $clientExceptFields, array $expect): void {
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

test('fields! fields', function (array $projectable, string $clientFields, string $clientExceptFields, array $expect): void {
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

test('fields invalid', function (): void {
    $class = new class
    {
        use Projectable;
    };

    $method = new ReflectionMethod($class, 'fields');
    $method->setAccessible(true);

    $_GET['fields'] = 'foo';

    expect($method->invoke(null, ''))->toBe([]);
});
