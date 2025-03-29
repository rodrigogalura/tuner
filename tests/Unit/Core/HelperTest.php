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
