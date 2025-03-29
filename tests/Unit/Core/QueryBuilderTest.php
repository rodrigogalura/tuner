<?php

use RGalura\ApiIgniter\Services\QueryBuilder as Query;

test('boolField', function (string $boolField, array $expect) {
    // Act & Assert
    expect(Query::boolField($boolField))->toBe($expect);
})
    ->with([
        'empty' => ['', []],
        'field only' => ['name', ['AND', 'name', false]],
        'and, field' => ['AND name', ['AND', 'name', false]],
        'or, field 2' => ['OR name', ['OR', 'name', false]],
        'and, not, field' => ['AND! name', ['AND', 'name', true]],
        'or, not, field' => ['OR! name', ['OR', 'name', true]],
        'and(lowercase), field' => ['and name', ['AND', 'name', false]],
        'or(lowercase), field' => ['or! name', ['OR', 'name', true]],
    ]);

test('comparisonOperator', function (string $val, array $expect) {
    // Act & Assert
    expect(Query::comparisonOperator($val))->toBe($expect);
})
    ->with([
        'empty' => ['', ['=', '']],
        'operator only' => ['=', ['=', '']],
        'value only' => ['john', ['=', 'john']],
        'operator and value' => ['=john', ['=', 'john']],
        'operator space value' => ['= john', ['=', 'john']],
        '>' => ['>john', ['>', 'john']],
        '> with space' => ['> john', ['>', 'john']],
        '<' => ['<john', ['<', 'john']],
        '< with space' => ['< john', ['<', 'john']],
        '>=' => ['>=john', ['>=', 'john']],
        '>= with space' => ['>= john', ['>=', 'john']],
        '<=' => ['<=john', ['<=', 'john']],
        '<= with space' => ['<= john', ['<=', 'john']],
        '<>' => ['<>john', ['<>', 'john']],
        '<> with space' => ['<> john', ['<>', 'john']],
    ]);
