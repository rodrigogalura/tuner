<?php

use Laradigs\Tweaker\TruthTable;

test('intersect', function ($p, $q, $p_INTERSECT_q, $p_EXCEPT_q) {
    // Prepare
    $truthTable = new TruthTable(['id', 'name']);

    // Act & Assert
    expect($truthTable->intersect($p, $q))->toBe($p_INTERSECT_q);
})->with('truth-table');

test('except', function ($p, $q, $p_INTERSECT_q, $p_EXCEPT_q) {
    // Prepare
    $truthTable = new TruthTable(['id', 'name']);

    // Act & Assert
    expect($truthTable->except($p, $q))->toBe($p_EXCEPT_q);
})->with('truth-table');
