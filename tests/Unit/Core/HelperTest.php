<?php

use function RGalura\ApiIgniter\is_multi_array;
use function RGalura\ApiIgniter\filter_explode;

test('filter_explode', function (string $input, string $delimiter, array $expect): void {
    // Act & Assert
    expect(filter_explode($input, $delimiter))->toBe($expect);
})
    ->with([
        'empty' => ['', ',', []],
        'single' => ['johndoe@test.com', ',', ['johndoe@test.com']],
        'single 2' => ['johndoe@test.com', ' ', ['johndoe@test.com']],
        'multiple 2' => ['johndoe@test.com foobar@test.com', ' ', ['johndoe@test.com', 'foobar@test.com']],
        'multiple' => ['johndoe@test.com,foobar@test.com', ',', ['johndoe@test.com', 'foobar@test.com']],
        'multiple with many space' => ['johndoe@test.com,       foobar@test.com', ',', ['johndoe@test.com', 'foobar@test.com']],
    ]);

test('is_multi_array', function ($array, $isMultiArray) {
    expect(is_multi_array($array))->toBe($isMultiArray);
})->with([
    'plainNumericArray' => ['array' => [1, 2, 3], 'isMultiArray' => false],
    'plainAlphabeticArray' => ['array' => ['a', 'b', 'c'], 'isMultiArray' => false],
    'plainAlphanumericrray' => ['array' => [1, 2, 3, 'a', 'b', 'c'], 'isMultiArray' => false],

    'numericMultiArray' => ['array' => [[1, 2, 3]], 'isMultiArray' => true],
    'alphaMultiArray' => ['array' => [['a', 'b', 'c']], 'isMultiArray' => true],
    'alphanumericMultiArray' => ['array' => [[1, 2, 3], ['a', 'b', 'c']], 'isMultiArray' => true],
])->only();
