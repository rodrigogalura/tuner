<?php

use Laradigs\Tweaker\V31\Matrix;

test('Matrix', function ($variables, $matrix2d): void {
    $m = new Matrix($variables);

    expect($m->handle())->toBe($matrix2d);
})->with([
    '2x2' => [
        'variables' => [
            ['a', 'b'],
            ['c', 'd'],
        ],
        'matrix2d' => [
            ['a', 'c'],
            ['a', 'd'],
            ['b', 'c'],
            ['b', 'd'],
        ],
    ],

    '2x3' => [
        'variables' => [
            ['a', 'b'],
            ['c', 'd'],
            ['e', 'f'],
        ],
        'matrix2d' => [
            ['a', 'c', 'e'],
            ['a', 'c', 'f'],
            ['a', 'd', 'e'],
            ['a', 'd', 'f'],

            ['b', 'c', 'e'],
            ['b', 'c', 'f'],
            ['b', 'd', 'e'],
            ['b', 'd', 'f'],
        ],
    ],

    '3x2' => [
        'variables' => [
            ['a', 'b', 'c'],
            ['d', 'e', 'f'],
        ],
        'matrix2d' => [
            ['a', 'd'],
            ['a', 'e'],
            ['a', 'f'],

            ['b', 'd'],
            ['b', 'e'],
            ['b', 'f'],

            ['c', 'd'],
            ['c', 'e'],
            ['c', 'f'],
        ],
    ],

    '3x3' => [
        'variables' => [
            ['a', 'b', 'c'],
            ['d', 'e', 'f'],
            ['g', 'h', 'i'],
        ],
        'matrix2d' => [
            ['a', 'd', 'g'],
            ['a', 'd', 'h'],
            ['a', 'd', 'i'],
            ['a', 'e', 'g'],
            ['a', 'e', 'h'],
            ['a', 'e', 'i'],
            ['a', 'f', 'g'],
            ['a', 'f', 'h'],
            ['a', 'f', 'i'],

            ['b', 'd', 'g'],
            ['b', 'd', 'h'],
            ['b', 'd', 'i'],
            ['b', 'e', 'g'],
            ['b', 'e', 'h'],
            ['b', 'e', 'i'],
            ['b', 'f', 'g'],
            ['b', 'f', 'h'],
            ['b', 'f', 'i'],

            ['c', 'd', 'g'],
            ['c', 'd', 'h'],
            ['c', 'd', 'i'],
            ['c', 'e', 'g'],
            ['c', 'e', 'h'],
            ['c', 'e', 'i'],
            ['c', 'f', 'g'],
            ['c', 'f', 'h'],
            ['c', 'f', 'i'],
        ],
    ],
]);
