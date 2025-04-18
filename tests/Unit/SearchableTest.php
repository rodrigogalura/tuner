<?php

use RGalura\ApiIgniter\Searchable;

test('searchFilter', function (array|string $searchableFields, array $clientFilter, array $expect) {
    // Prepare
    $class = new class
    {
        use Searchable;
    };

    $method = new ReflectionMethod($class, 'searchFilter');
    $method->setAccessible(true);

    $_GET['search'] = $clientFilter;

    // Act & Assert
    expect($method->invoke(null, $searchableFields))->toBe($expect);
})
    ->with([
        [['*'], [], []],
        [[], ['*' => 'foo'], []],
        [['*'], ['name' => 'foo'], ['name' => '%foo%']],
        ['*', ['name, lname' => '*foo*'], ['name, lname' => '%foo%']],
        [['name'], ['name, lname' => '*bar'], ['name' => '%bar']],
        ['name, lname', ['lname' => 'bar*'], ['lname' => 'bar%']],
        [['name', 'lname'], ['name, lname' => 'bar*'], ['name, lname' => 'bar%']],
    ]);
