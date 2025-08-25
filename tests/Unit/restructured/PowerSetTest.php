<?php

use Laradigs\Tweaker\V31\PowerSet;

test('Power Set', function ($set, $powerSet) {
    expect((new PowerSet($set))->handle())->toBe($powerSet);
})->with([
    [
        'set' => ['a'],
        'powerSet' => ['', 'a']
    ],

    [
        'set' => ['a', 'b'],
        'powerSet' => [
            '',
            'a', 'b',
            'a, b'
        ]
    ],

    [
        'set' => ['a', 'b', 'c'],
        'powerSet' => [
            '',
            'a', 'b', 'c',
            'a, b', 'a, c', 'b, c',
            'a, b, c'
        ],
    ],

    [
        'set' => ['a', 'b', 'c', 'd'],
        'powerSet' => [
            '',
            'a', 'b', 'c', 'd',
            'a, b', 'a, c', 'a, d', 'b, c', 'b, d', 'c, d',
            'a, b, c', 'a, b, d', 'a, c, d', 'b, c, d',
            'a, b, c, d'
        ],
    ],

    [
        'set' => ['a', 'b', 'c', 'd', 'e'],
        'powerSet' => [
            '',
            'a', 'b', 'c', 'd', 'e',
            'a, b', 'a, c', 'a, d', 'a, e',
            'b, c', 'b, d', 'b, e',
            'c, d', 'c, e',
            'd, e',

            'a, b, c', 'a, b, d', 'a, b, e', 'a, c, d', 'a, c, e', 'a, d, e',
            'b, c, d', 'b, c, e', 'b, d, e',
            'c, d, e',

            'a, b, c, d', 'a, b, c, e', 'a, b, d, e', 'a, c, d, e',
            'b, c, d, e',

            'a, b, c, d, e'
        ],
    ]
]);
