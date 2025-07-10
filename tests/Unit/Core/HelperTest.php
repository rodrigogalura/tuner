<?php

use function RGalura\ApiIgniter\filter_explode;

test('filter_explode', function (string $input, string $delimiter, array $expect) {
    // Act & Assert
    expect(filter_explode($input, $delimiter))->toBe($expect);
})
    ->with([
        'empty' => ['', ',', []],
        'single' => ['johndoe@test.com', ',', ['johndoe@test.com']],
        'single 2' => ['johndoe@test.com', ' ', ['johndoe@test.com']],
        'multiple' => ['johndoe@test.com,foobar@test.com', ',', ['johndoe@test.com', 'foobar@test.com']],
        'multiple 2' => ['johndoe@test.com foobar@test.com', ' ', ['johndoe@test.com', 'foobar@test.com']],
        'multiple' => ['johndoe@test.com,foobar@test.com', ',', ['johndoe@test.com', 'foobar@test.com']],
        'multiple with many space' => ['johndoe@test.com,       foobar@test.com', ',', ['johndoe@test.com', 'foobar@test.com']],
    ]);

// test('array_insert', function (array $input, int $position, $item, array $expect) {
//     // Act
//     array_insert($input, $position, $item);

//     // Assert
//     expect($input)->toBe($expect);
// })
// ->with([
//     'insert into index 0' => ['input' => ['a', 'b', 'c', 'd'], 'position' => 0, 'item' => 'x', 'expect' => ['x', 'a', 'b', 'c', 'd']],
//     'insert into index 1' => ['input' => ['a', 'b', 'c', 'd'], 'position' => 1, 'item' => 'x', 'expect' => ['a', 'x', 'b', 'c', 'd']],
//     'insert into index 2' => ['input' => ['a', 'b', 'c', 'd'], 'position' => 2, 'item' => 'x', 'expect' => ['a', 'b', 'x', 'c', 'd']],
//     'insert into index -1' => ['input' => ['a', 'b', 'c', 'd'], 'position' => -1, 'item' => 'x', 'expect' => ['a', 'b', 'c', 'd', 'x']],
// ]);

// test('array_insert_multiple', function (array $arrayFrom, array $arrayTo, array $expect) {
//     // Act
//     array_insert_multiple($arrayFrom, $arrayTo);

//     // Assert
//     expect($arrayFrom)->toBe($expect);
// })
// ->with([
//     ['arrayFrom' => ['a', 'b', 'c', 'd'], 'arrayTo' => ['x', 'y'], 'expect' => ['x', 'y', 'a', 'b', 'c', 'd']],
//     ['arrayFrom' => ['a', 'b', 'c', 'd'], 'arrayTo' => [1 => 'x', 2 => 'y'], 'expect' => ['a', 'x', 'y', 'b', 'c', 'd']],
//     ['arrayFrom' => ['a', 'b', 'c', 'd'], 'arrayTo' => [1 => 'x', 3 => 'y'], 'expect' => ['a', 'x', 'b', 'y', 'c', 'd']],
//     ['arrayFrom' => ['a', 'b', 'c', 'd'], 'arrayTo' => [-1 => 'x', 1 => 'y'], 'expect' => ['a', 'y', 'b', 'c', 'd', 'x']],
// ]);
