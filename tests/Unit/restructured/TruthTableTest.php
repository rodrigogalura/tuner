<?php

use Laradigs\Tweaker\V31\TruthTable\TruthTable;

// use Laradigs\Tweaker\TruthTable;

// test('intersect', function ($p, $q, $p_INTERSECT_q, $p_EXCEPT_q): void {
//     // Prepare
//     $truthTable = new TruthTable(['id', 'name']);

//     // Act & Assert
//     expect($truthTable->intersect($p, $q))->toBe($p_INTERSECT_q);
// })->with('truth-table');

// test('except', function ($p, $q, $p_INTERSECT_q, $p_EXCEPT_q): void {
//     // Prepare
//     $truthTable = new TruthTable(['id', 'name']);

//     // Act & Assert
//     expect($truthTable->except($p, $q))->toBe($p_EXCEPT_q);
// })->with('truth-table');

// test('Truth Table Matrix 3 Variable', function () {
//     $t = new TruthTable;

//     $variables = [
//         ['*', 'b', 'c'],
//         ['d', 'e', 'f'],
//         ['g', 'h', 'i'],
//     ];

//     expect($t->matrix2($variables))->toBe(
//         [
//             [
//                 [
//                     ['*', 'd', 'g'],
//                     ['*', 'd', 'h'],
//                     ['*', 'd', 'i'],
//                 ],
//                 [
//                     ['*', 'e', 'g'],
//                     ['*', 'e', 'h'],
//                     ['*', 'e', 'i'],
//                 ],
//                 [
//                     ['*', 'f', 'g'],
//                     ['*', 'f', 'h'],
//                     ['*', 'f', 'i'],
//                 ],
//             ],
//             [
//                 [
//                     ['b', 'd', 'g'],
//                     ['b', 'd', 'h'],
//                     ['b', 'd', 'i'],
//                 ],
//                 [
//                     ['b', 'e', 'g'],
//                     ['b', 'e', 'h'],
//                     ['b', 'e', 'i'],
//                 ],
//                 [
//                     ['b', 'f', 'g'],
//                     ['b', 'f', 'h'],
//                     ['b', 'f', 'i'],
//                 ],
//             ],
//             [
//                 [
//                     ['c', 'd', 'g'],
//                     ['c', 'd', 'h'],
//                     ['c', 'd', 'i'],
//                 ],
//                 [
//                     ['c', 'e', 'g'],
//                     ['c', 'e', 'h'],
//                     ['c', 'e', 'i'],
//                 ],
//                 [
//                     ['c', 'f', 'g'],
//                     ['c', 'f', 'h'],
//                     ['c', 'f', 'i'],
//                 ],
//             ],
//         ]
//     );
// });

test('Truth Table Matrix 3d Variable', function ($variables, $matrix2d) {
    $t = new TruthTable;

    expect($t->matrix2d($variables))->toBe($matrix2d);
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
        ]
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
        ]
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
        ]
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
        ]
    ],
]);
