<?php

return [
    'projection' => [
        'include_key' => 'fields',
        'exclude_key' => 'fields!',
    ],

    'search' => [
        'key' => 'search',
        'minimum_length' => 2,
    ],

    'sort' => [
        'key' => 'search',
        'minimum_length' => 2,
    ],
];
