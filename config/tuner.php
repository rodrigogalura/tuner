<?php

return [
    'projection' => [
        'key' => [
            'intersect' => env('TUNER_INTERSECT_KEY', 'fields'),
            'except' => env('TUNER_EXCEPT_KEY', 'fields!'),
        ],
    ],

    'sort' => [
        'key' => env('TUNER_SORT_KEY', 'sort'),
    ],

    'search' => [
        'key' => env('TUNER_SEARCH_KEY', 'search'),
        'minimum_length' => env('TUNER_SEARCH_MINIMUM_LENGTH', 2),
    ],

    'filter' => [
        'key' => array_combine($keys = [
            env('TUNER_FILTER_KEY', 'filter'),
            env('TUNER_IN_KEY', 'in'),
            env('TUNER_BETWEEN_KEY', 'between'),
        ], $keys),
    ],

    'limit' => [
        'key' => array_combine($keys = [
            env('TUNER_LIMIT_KEY', 'limit'),
            env('TUNER_LIMIT_KEY', 'offset'),
        ], $keys),
    ],

    'pagination' => [
        'key' => env('TUNER_PAGINATION_KEY', 'page-size'),
    ],

    'expansion' => [
        'key' => env('TUNER_EXPANSION_KEY', 'expand'),
        'separator' => env('TUNER_EXPANSION_SEPARATOR', '_'),
    ],
];
