<?php

return [
    'projection' => [
        'key' => [
            'intersect' => 'columns',
            'except' => 'columns!',
        ],
    ],

    'sort' => [
        'key' => 'sort',
    ],

    'search' => [
        'key' => 'search',
        'minimum_length' => 2,
    ],

    'filter' => [
        'key' => array_combine($keys = ['filter', 'in', 'between'], $keys),
    ],

    'limit' => [
        'key' => array_combine($keys = ['limit', 'offset'], $keys),
    ],

    'pagination' => [
        'key' => 'page-size',
    ],
];
