<?php

return [
    'projection' => [
        'intersect_key' => 'fields',
        'except_key' => 'fields!',
    ],

    'search' => [
        'key' => 'search',
        'minimum_length' => 2,
    ],

    'sort' => [
        'key' => 'sort'
    ],
];
