<?php

return [
    'projection' => [
        'key' => [
            'intersect' => 'columns',
            'except' => 'columns!',
        ],
    ],

    'search' => [
        'key' => 'search',
        'minimum_length' => 2,
    ],

    'sort' => [
        'key' => 'sort',
    ],

    'filter' => [
        'key' => array_combine($keys = ['filter', 'in', 'between'], $keys),
    ],
];
