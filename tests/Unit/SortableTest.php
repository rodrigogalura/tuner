<?php

use RGalura\ApiIgniter\Sortable;

test('sort', function (array|string $sortableFields, array $clientSort, array $expect) {
    // Prepare
    $class = new class
    {
        use Sortable;
    };

    $method = new ReflectionMethod($class, 'sort');
    $method->setAccessible(true);

    $_GET['sort'] = $clientSort;

    // Act & Assert
    expect($method->invoke(null, $sortableFields))->toBe($expect);
})
    ->with([
        [[], ['*'], []],
        [['*'], [], []],
        [['*'], ['foo' => ''], ['foo' => 'ASC']],
        [['foo'], ['foo' => 'd', 'john' => 'doe'], ['foo' => 'DESC']],
        ['john', ['foo' => 'd', 'john' => 'des'], ['john' => 'DESC']],
        [['foo', 'john'], ['foo' => 'desc', 'john' => 'descending'], ['foo' => 'DESC', 'john' => 'DESC']],
        ['foo, baz', ['foo' => '-', 'john' => 'doe'], ['foo' => 'DESC']],
    ]);
