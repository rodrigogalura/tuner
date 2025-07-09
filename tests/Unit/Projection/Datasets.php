<?php

dataset('truth-table', [
    // == Dataset 1
    [
        'projectableFields' => ['*'],       'definedFields' => ['*'],               'clientInput' => '*',               'expectedResult' => ['fields' => ['id', 'name'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['*'],       'definedFields' => ['*'],               'clientInput' => 'id',              'expectedResult' => ['fields' => ['id'], 'fields!' => ['name']],
    ],
    [
        'projectableFields' => ['*'],       'definedFields' => ['*'],               'clientInput' => 'name',            'expectedResult' => ['fields' => ['name'], 'fields!' => ['id']],
    ],
    [
        'projectableFields' => ['*'],       'definedFields' => ['*'],               'clientInput' => 'id, name',        'expectedResult' => ['fields' => ['id', 'name'], 'fields!' => []],
    ],

    [
        'projectableFields' => ['*'],       'definedFields' => ['id'],              'clientInput' => '*',               'expectedResult' => ['fields' => ['id'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['*'],       'definedFields' => ['id'],              'clientInput' => 'id',              'expectedResult' => ['fields' => ['id'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['*'],       'definedFields' => ['id'],              'clientInput' => 'name',            'expectedResult' => ['fields' => [], 'fields!' => ['id']],
    ],
    [
        'projectableFields' => ['*'],       'definedFields' => ['id'],              'clientInput' => 'id, name',        'expectedResult' => ['fields' => ['id'], 'fields!' => []],
    ],

    [
        'projectableFields' => ['*'],       'definedFields' => ['name'],            'clientInput' => '*',               'expectedResult' => ['fields' => ['name'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['*'],       'definedFields' => ['name'],            'clientInput' => 'id',              'expectedResult' => ['fields' => [], 'fields!' => ['name']],
    ],
    [
        'projectableFields' => ['*'],       'definedFields' => ['name'],            'clientInput' => 'name',            'expectedResult' => ['fields' => ['name'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['*'],       'definedFields' => ['name'],            'clientInput' => 'id, name',        'expectedResult' => ['fields' => ['name'], 'fields!' => []],
    ],

    [
        'projectableFields' => ['*'],       'definedFields' => ['id', 'name'],      'clientInput' => '*',               'expectedResult' => ['fields' => ['id', 'name'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['*'],       'definedFields' => ['id', 'name'],      'clientInput' => 'id',              'expectedResult' => ['fields' => ['id'], 'fields!' => ['name']],
    ],
    [
        'projectableFields' => ['*'],       'definedFields' => ['id', 'name'],      'clientInput' => 'name',            'expectedResult' => ['fields' => ['name'], 'fields!' => ['id']],
    ],
    [
        'projectableFields' => ['*'],       'definedFields' => ['id', 'name'],      'clientInput' => 'id, name',        'expectedResult' => ['fields' => ['id', 'name'], 'fields!' => []],
    ],

    // == Dataset 2
    [
        'projectableFields' => ['id'],       'definedFields' => ['*'],               'clientInput' => '*',               'expectedResult' => ['fields' => ['id'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['id'],       'definedFields' => ['*'],               'clientInput' => 'id',              'expectedResult' => ['fields' => ['id'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['id'],       'definedFields' => ['*'],               'clientInput' => 'name',            'expectedResult' => ['fields' => [], 'fields!' => ['id']],
    ],
    [
        'projectableFields' => ['id'],       'definedFields' => ['*'],               'clientInput' => 'id, name',        'expectedResult' => ['fields' => ['id'], 'fields!' => []],
    ],

    [
        'projectableFields' => ['id'],       'definedFields' => ['id'],              'clientInput' => '*',               'expectedResult' => ['fields' => ['id'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['id'],       'definedFields' => ['id'],              'clientInput' => 'id',              'expectedResult' => ['fields' => ['id'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['id'],       'definedFields' => ['id'],              'clientInput' => 'name',            'expectedResult' => ['fields' => [], 'fields!' => ['id']],
    ],
    [
        'projectableFields' => ['id'],       'definedFields' => ['id'],              'clientInput' => 'id, name',        'expectedResult' => ['fields' => ['id'], 'fields!' => []],
    ],

    /* These scenarios are on the other test suites
    [
        'projectableFields' => ['id'],       'definedFields' => ['name'],            'clientInput' => '*',               'expectedResult' => ['fields' => null, 'fields!' => []],
    ],
    [
        'projectableFields' => ['id'],       'definedFields' => ['name'],            'clientInput' => 'id',              'expectedResult' => ['fields' => null, 'fields!' => []],
    ],
    [
        'projectableFields' => ['id'],       'definedFields' => ['name'],            'clientInput' => 'name',            'expectedResult' => ['fields' => null, 'fields!' => []],
    ],
    [
        'projectableFields' => ['id'],       'definedFields' => ['name'],            'clientInput' => 'id, name',        'expectedResult' => ['fields' => null, 'fields!' => []],
    ],
    */

    [
        'projectableFields' => ['id'],       'definedFields' => ['id', 'name'],      'clientInput' => '*',               'expectedResult' => ['fields' => ['id'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['id'],       'definedFields' => ['id', 'name'],      'clientInput' => 'id',              'expectedResult' => ['fields' => ['id'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['id'],       'definedFields' => ['id', 'name'],      'clientInput' => 'name',            'expectedResult' => ['fields' => [], 'fields!' => ['id']],
    ],
    [
        'projectableFields' => ['id'],       'definedFields' => ['id', 'name'],      'clientInput' => 'id, name',        'expectedResult' => ['fields' => ['id'], 'fields!' => []],
    ],

    // // == Dataset 3
    [
        'projectableFields' => ['name'],       'definedFields' => ['*'],               'clientInput' => '*',               'expectedResult' => ['fields' => ['name'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['name'],       'definedFields' => ['*'],               'clientInput' => 'id',              'expectedResult' => ['fields' => [], 'fields!' => ['name']],
    ],
    [
        'projectableFields' => ['name'],       'definedFields' => ['*'],               'clientInput' => 'name',            'expectedResult' => ['fields' => ['name'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['name'],       'definedFields' => ['*'],               'clientInput' => 'id, name',        'expectedResult' => ['fields' => ['name'], 'fields!' => []],
    ],

    /* These scenarios are on the other test suites
    [
        'projectableFields' => ['name'],       'definedFields' => ['id'],              'clientInput' => '*',               'expectedResult' => ['fields' => ['id'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['name'],       'definedFields' => ['id'],              'clientInput' => 'id',              'expectedResult' => ['fields' => ['id'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['name'],       'definedFields' => ['id'],              'clientInput' => 'name',            'expectedResult' => ['fields' => [], 'fields!' => []],
    ],
    [
        'projectableFields' => ['name'],       'definedFields' => ['id'],              'clientInput' => 'id, name',        'expectedResult' => ['fields' => ['id'], 'fields!' => []],
    ],
    */

    [
        'projectableFields' => ['name'],       'definedFields' => ['name'],            'clientInput' => '*',               'expectedResult' => ['fields' => ['name'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['name'],       'definedFields' => ['name'],            'clientInput' => 'id',              'expectedResult' => ['fields' => [], 'fields!' => ['name']],
    ],
    [
        'projectableFields' => ['name'],       'definedFields' => ['name'],            'clientInput' => 'name',            'expectedResult' => ['fields' => ['name'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['name'],       'definedFields' => ['name'],            'clientInput' => 'id, name',        'expectedResult' => ['fields' => ['name'], 'fields!' => []],
    ],

    [
        'projectableFields' => ['name'],       'definedFields' => ['id', 'name'],      'clientInput' => '*',               'expectedResult' => ['fields' => ['name'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['name'],       'definedFields' => ['id', 'name'],      'clientInput' => 'id',              'expectedResult' => ['fields' => [], 'fields!' => ['name']],
    ],
    [
        'projectableFields' => ['name'],       'definedFields' => ['id', 'name'],      'clientInput' => 'name',            'expectedResult' => ['fields' => ['name'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['name'],       'definedFields' => ['id', 'name'],      'clientInput' => 'id, name',        'expectedResult' => ['fields' => ['name'], 'fields!' => []],
    ],

    // // == Dataset 4
    [
        'projectableFields' => ['id', 'name'],       'definedFields' => ['*'],               'clientInput' => '*',               'expectedResult' => ['fields' => ['id', 'name'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['id', 'name'],       'definedFields' => ['*'],               'clientInput' => 'id',              'expectedResult' => ['fields' => ['id'], 'fields!' => ['name']],
    ],
    [
        'projectableFields' => ['id', 'name'],       'definedFields' => ['*'],               'clientInput' => 'name',            'expectedResult' => ['fields' => ['name'], 'fields!' => ['id']],
    ],
    [
        'projectableFields' => ['id', 'name'],       'definedFields' => ['*'],               'clientInput' => 'id, name',        'expectedResult' => ['fields' => ['id', 'name'], 'fields!' => []],
    ],

    [
        'projectableFields' => ['id', 'name'],       'definedFields' => ['id'],              'clientInput' => '*',               'expectedResult' => ['fields' => ['id'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['id', 'name'],       'definedFields' => ['id'],              'clientInput' => 'id',              'expectedResult' => ['fields' => ['id'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['id', 'name'],       'definedFields' => ['id'],              'clientInput' => 'name',            'expectedResult' => ['fields' => [], 'fields!' => ['id']],
    ],
    [
        'projectableFields' => ['id', 'name'],       'definedFields' => ['id'],              'clientInput' => 'id, name',        'expectedResult' => ['fields' => ['id'], 'fields!' => []],
    ],

    [
        'projectableFields' => ['id', 'name'],       'definedFields' => ['name'],            'clientInput' => '*',               'expectedResult' => ['fields' => ['name'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['id', 'name'],       'definedFields' => ['name'],            'clientInput' => 'id',              'expectedResult' => ['fields' => [], 'fields!' => ['name']],
    ],
    [
        'projectableFields' => ['id', 'name'],       'definedFields' => ['name'],            'clientInput' => 'name',            'expectedResult' => ['fields' => ['name'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['id', 'name'],       'definedFields' => ['name'],            'clientInput' => 'id, name',        'expectedResult' => ['fields' => ['name'], 'fields!' => []],
    ],

    [
        'projectableFields' => ['id', 'name'],       'definedFields' => ['id', 'name'],      'clientInput' => '*',               'expectedResult' => ['fields' => ['id', 'name'], 'fields!' => []],
    ],
    [
        'projectableFields' => ['id', 'name'],       'definedFields' => ['id', 'name'],      'clientInput' => 'id',              'expectedResult' => ['fields' => ['id'], 'fields!' => ['name']],
    ],
    [
        'projectableFields' => ['id', 'name'],       'definedFields' => ['id', 'name'],      'clientInput' => 'name',            'expectedResult' => ['fields' => ['name'], 'fields!' => ['id']],
    ],
    [
        'projectableFields' => ['id', 'name'],       'definedFields' => ['id', 'name'],      'clientInput' => 'id, name',        'expectedResult' => ['fields' => ['id', 'name'], 'fields!' => []],
    ],
]);
