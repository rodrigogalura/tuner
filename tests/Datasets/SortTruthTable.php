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
            0 => 'id',
        ],
        'clientFields' => [
            0 => 'id',
            1 => 'name',
        ],
        'resultFields' => [
            0 => 'id',
        ],
    ],
    2 => [
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
    3 => [
        'sortableFields' => [
            0 => 'name',
        ],
        'clientFields' => [
            0 => 'id',
            1 => 'name',
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
        ],
        'resultFields' => [
            0 => 'id',
        ],
    ],
    5 => [
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
    6 => [
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

dataset('sort-keyword-truth-table', [
    9 => [
        'clientDirection' => '',
        'resultDirection' => 'ASC',
    ],
    10 => [
        'clientDirection' => 'd',
        'resultDirection' => 'DESC',
    ],
    11 => [
        'clientDirection' => 'des',
        'resultDirection' => 'DESC',
    ],
    12 => [
        'clientDirection' => 'desc',
        'resultDirection' => 'DESC',
    ],
    13 => [
        'clientDirection' => 'descending',
        'resultDirection' => 'DESC',
    ],
    14 => [
        'clientDirection' => '-',
        'resultDirection' => 'DESC',
    ],
]);
