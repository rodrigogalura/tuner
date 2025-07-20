<?php

dataset('search-fields-truth-table', [
  0 =>
  [
    'searchableFields' =>
    [
      0 => '*',
    ],
    'clientFields' => '*',
    'resultFields' => 'id, name',
  ],
  1 =>
  [
    'searchableFields' =>
    [
      0 => '*',
    ],
    'clientFields' => 'id',
    'resultFields' => 'id',
  ],
  2 =>
  [
    'searchableFields' =>
    [
      0 => '*',
    ],
    'clientFields' => 'name',
    'resultFields' => 'name',
  ],
  3 =>
  [
    'searchableFields' =>
    [
      0 => '*',
    ],
    'clientFields' => 'id, name',
    'resultFields' => 'id, name',
  ],
  4 =>
  [
    'searchableFields' =>
    [
      0 => 'id',
    ],
    'clientFields' => '*',
    'resultFields' => 'id',
  ],
  5 =>
  [
    'searchableFields' =>
    [
      0 => 'id',
    ],
    'clientFields' => 'id',
    'resultFields' => 'id',
  ],
  6 =>
  [
    'searchableFields' =>
    [
      0 => 'id',
    ],
    'clientFields' => 'id, name',
    'resultFields' => 'id',
  ],
  7 =>
  [
    'searchableFields' =>
    [
      0 => 'name',
    ],
    'clientFields' => '*',
    'resultFields' => 'name',
  ],
  8 =>
  [
    'searchableFields' =>
    [
      0 => 'name',
    ],
    'clientFields' => 'name',
    'resultFields' => 'name',
  ],
  9 =>
  [
    'searchableFields' =>
    [
      0 => 'name',
    ],
    'clientFields' => 'id, name',
    'resultFields' => 'name',
  ],
  10 =>
  [
    'searchableFields' =>
    [
      0 => 'id',
      1 => 'name',
    ],
    'clientFields' => '*',
    'resultFields' => 'id, name',
  ],
  11 =>
  [
    'searchableFields' =>
    [
      0 => 'id',
      1 => 'name',
    ],
    'clientFields' => 'id',
    'resultFields' => 'id',
  ],
  12 =>
  [
    'searchableFields' =>
    [
      0 => 'id',
      1 => 'name',
    ],
    'clientFields' => 'name',
    'resultFields' => 'name',
  ],
  13 =>
  [
    'searchableFields' =>
    [
      0 => 'id',
      1 => 'name',
    ],
    'clientFields' => 'id, name',
    'resultFields' => 'id, name',
  ],
]);

dataset('search-keyword-truth-table', [
    15 =>
    [
      'clientKeyword' => 'foo',
      'resultKeyword' => '%foo%',
    ],
    16 =>
    [
      'clientKeyword' => '*foo*',
      'resultKeyword' => '%foo%',
    ],
    17 =>
    [
      'clientKeyword' => '*foo',
      'resultKeyword' => '%foo',
    ],
    18 =>
    [
      'clientKeyword' => 'foo*',
      'resultKeyword' => 'foo%',
    ],
]);
