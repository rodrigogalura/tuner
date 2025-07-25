<?php

dataset('sort-fields-truth-table', [
    0 => [
        'sortableFields' => [
            0 => 'id',
        ],
        'clientFields' => [
            0 => 'id',
        ],
        'resultFields' => [
            0 => 'id',
        ],
    ],
    1 => [
        'sortableFields' => [
            0 => 'name',
        ],
        'clientFields' => [
            0 => 'name',
        ],
        'resultFields' => [
            0 => 'name',
        ],
    ],
    2 => [
        'sortableFields' => [
            0 => 'id',
            1 => 'name',
        ],
        'clientFields' => [
            0 => 'id',
        ],
        'resultFields' => [
            0 => 'id',
        ],
    ],
    3 => [
        'sortableFields' => [
            0 => 'id',
            1 => 'name',
        ],
        'clientFields' => [
            0 => 'name',
        ],
        'resultFields' => [
            0 => 'name',
        ],
    ],
    4 => [
        'sortableFields' => [
            0 => 'id',
            1 => 'name',
        ],
        'clientFields' => [
            0 => 'id',
            1 => 'name',
        ],
        'resultFields' => [
            0 => 'id',
            1 => 'name',
        ],
    ],
]);

dataset('sort-direction-truth-table', [
    7 => [
        'clientDirection' => '',
        'resultDirection' => 'ASC',
    ],
    8 => [
        'clientDirection' => 'd',
        'resultDirection' => 'DESC',
    ],
    9 => [
        'clientDirection' => 'des',
        'resultDirection' => 'DESC',
    ],
    10 => [
        'clientDirection' => 'desc',
        'resultDirection' => 'DESC',
    ],
    11 => [
        'clientDirection' => 'descending',
        'resultDirection' => 'DESC',
    ],
    12 => [
        'clientDirection' => '-',
        'resultDirection' => 'DESC',
    ],
]);
