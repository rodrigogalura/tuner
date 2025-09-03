<?php

return [
    'projection' => [
        'intersect_keyword' => 'columns',
        'except_keyword' => 'columns!',
    ],

    'search' => [
        'keyword' => 'search',
        'minimum_length' => 2,
    ],

    'sort' => [
        'keyword' => 'sort',
    ],
];
