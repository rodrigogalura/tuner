<?php

use RGalura\ApiIgniter\BetweenFilterable;
use RGalura\ApiIgniter\Expandable;
use RGalura\ApiIgniter\Filterable;
use RGalura\ApiIgniter\InFilterable;
use RGalura\ApiIgniter\Projectable;
use RGalura\ApiIgniter\Searchable;
use RGalura\ApiIgniter\Sortable;

dataset('expandable', [
    'allow all fields' => [
        [
            'posts' => [
                'table' => 'posts',
                'projectable' => [
                    'fields' => ['*'],
                    'columnListing' => ['id', 'name', 'foo', 'bar'],
                ],
                'filterable_fields' => ['*'],
                'searchable_fields' => ['*'],
                'sortable_fields' => ['*'],
                'fk' => 'user_id',
            ],
            'siblings' => [
                'table' => 'siblings',
                'projectable' => [
                    'fields' => ['*'],
                    'columnListing' => ['id', 'name', 'foo', 'bar'],
                ],
                'filterable_fields' => ['*'],
                'searchable_fields' => ['*'],
                'sortable_fields' => ['*'],
                'fk' => 'user_id',
            ],
        ],
    ],
]);

beforeEach(function (): void {
    $_GET = [];
});

test('expand fields', function (array $expandable, array $expand, string $fields, array $expect): void {
    // Prepare
    $class = new class
    {
        use BetweenFilterable, Filterable, InFilterable, Projectable, Searchable, Sortable;
        use Expandable;
    };

    $method = new ReflectionMethod($class, 'expand');
    $method->setAccessible(true);

    $_GET['expand'] = $expand;

    $alias = current($expand);
    $_GET["{$alias}_fields"] = $fields;

    // Act & Assert
    expect($method->invoke(null, $expandable))->toBe($expect);
})
    ->with('expandable')
    ->with([
        'all fields' => [
            'expand' => ['posts' => 'p'],
            'fields' => '*',

            'expect' => [
                [
                    'relation' => 'posts',
                    'table' => 'posts',
                    'fields' => ['*'],
                    'filter' => [],
                    'inFilter' => [],
                    'betweenFilter' => [],
                    'searchFilter' => [],
                    'sort' => [],
                ],
            ],
        ],
        'defined fields' => [
            'expand' => ['posts' => 'p'],
            'fields' => 'id, title',

            'expect' => [
                [
                    'relation' => 'posts',
                    'table' => 'posts',
                    'fields' => ['id', 'title', 'user_id'],
                    'filter' => [],
                    'inFilter' => [],
                    'betweenFilter' => [],
                    'searchFilter' => [],
                    'sort' => [],
                ],
            ],
        ],
    ]);

test('expand fields!', function (array $expandable, array $expand, string $exceptFields, array $expect): void {
    // Prepare
    $class = new class
    {
        use BetweenFilterable, Filterable, InFilterable, Projectable, Searchable, Sortable;
        use Expandable;
    };

    $method = new ReflectionMethod($class, 'expand');
    $method->setAccessible(true);

    $_GET['expand'] = $expand;

    $alias = current($expand);
    $_GET["{$alias}_fields!"] = $exceptFields;

    // Act & Assert
    expect($method->invoke(null, $expandable))->toBe($expect);
})->with('expandable')
    ->with([
        [
            'expand' => ['posts' => 'p'],
            'exceptFields' => 'foo, bar',

            'expect' => [
                [
                    'relation' => 'posts',
                    'table' => 'posts',
                    'fields' => ['id', 'name', 'user_id'],
                    'filter' => [],
                    'inFilter' => [],
                    'betweenFilter' => [],
                    'searchFilter' => [],
                    'sort' => [],
                ],
            ],
        ],
    ]);

test('expand fields! fields', function (array $expandable, array $expand, string $exceptFields, array $expect): void {
    // Prepare
    $class = new class
    {
        use BetweenFilterable, Filterable, InFilterable, Projectable, Searchable, Sortable;
        use Expandable;
    };

    $method = new ReflectionMethod($class, 'expand');
    $method->setAccessible(true);

    $_GET['expand'] = $expand;

    $alias = current($expand);
    $_GET["{$alias}_fields"] = 'id, name, foo';
    $_GET["{$alias}_fields!"] = $exceptFields;

    // Act & Assert
    expect($method->invoke(null, $expandable))->toBe($expect);
})->with('expandable')
    ->with([
        [
            'expand' => ['posts' => 'p'],
            'exceptFields' => 'foo',

            'expect' => [
                [
                    'relation' => 'posts',
                    'table' => 'posts',
                    'fields' => ['id', 'name', 'user_id'],
                    'filter' => [],
                    'inFilter' => [],
                    'betweenFilter' => [],
                    'searchFilter' => [],
                    'sort' => [],
                ],
            ],
        ],
    ]);

test('expand filter', function (array $expandable, array $expand, string $fields, array $filter, array $expect): void {
    // Prepare
    $class = new class
    {
        use BetweenFilterable, Filterable, InFilterable, Projectable, Searchable, Sortable;
        use Expandable;
    };

    $method = new ReflectionMethod($class, 'expand');
    $method->setAccessible(true);

    $_GET['expand'] = $expand;

    $alias = current($expand);
    $_GET["{$alias}_fields"] = $fields;

    $_GET["{$alias}_filter"] = $filter;

    // Act & Assert
    expect($method->invoke(null, $expandable))->toBe($expect);
})->with('expandable')
    ->with([
        'all fields' => [
            'expand' => ['posts' => 'p'],
            'fields' => '*',
            'filter' => ['name' => 'bar'],

            'expect' => [
                [
                    'relation' => 'posts',
                    'table' => 'posts',
                    'fields' => ['*'],
                    'filter' => [
                        ['AND', 'name', false, '=', 'bar'],
                    ],
                    'inFilter' => [],
                    'betweenFilter' => [],
                    'searchFilter' => [],
                    'sort' => [],
                ],
            ],
        ],

        'defined fields' => [
            'expand' => ['posts' => 'p'],
            'fields' => 'id, title',
            'filter' => ['AND! title' => 'bar'],

            'expect' => [
                [
                    'relation' => 'posts',
                    'table' => 'posts',
                    'fields' => ['id', 'title', 'user_id'],
                    'filter' => [
                        ['AND', 'title', true, '=', 'bar'],
                    ],
                    'inFilter' => [],
                    'betweenFilter' => [],
                    'searchFilter' => [],
                    'sort' => [],
                ],
            ],
        ],
    ]);

test('expand in filter', function (array $expandable, array $expand, string $fields, array $inFilter, array $expect): void {
    // Prepare
    $class = new class
    {
        use BetweenFilterable, Filterable, InFilterable, Projectable, Searchable, Sortable;
        use Expandable;
    };

    $method = new ReflectionMethod($class, 'expand');
    $method->setAccessible(true);

    $_GET['expand'] = $expand;

    $alias = current($expand);
    $_GET["{$alias}_fields"] = $fields;

    $_GET["{$alias}_in"] = $inFilter;

    // Act & Assert
    expect($method->invoke(null, $expandable))->toBe($expect);
})->with('expandable')
    ->with([
        'all fields' => [
            'expand' => ['posts' => 'p'],
            'fields' => '*',
            'inFilter' => ['name' => 'bar'],

            'expect' => [
                [
                    'relation' => 'posts',
                    'table' => 'posts',
                    'fields' => ['*'],
                    'filter' => [],
                    'inFilter' => [
                        ['AND', 'name', false, ['bar']],
                    ],
                    'betweenFilter' => [],
                    'searchFilter' => [],
                    'sort' => [],
                ],
            ],
        ],

        'defined fields' => [
            'expand' => ['posts' => 'p'],
            'fields' => 'id, title',
            'inFilter' => ['AND! title' => 'bar'],

            'expect' => [
                [
                    'relation' => 'posts',
                    'table' => 'posts',
                    'fields' => ['id', 'title', 'user_id'],
                    'filter' => [],
                    'inFilter' => [
                        ['AND', 'title', true, ['bar']],
                    ],
                    'betweenFilter' => [],
                    'searchFilter' => [],
                    'sort' => [],
                ],
            ],
        ],
    ]);

test('expand between filter', function (array $expandable, array $expand, string $fields, array $betweenFilter, array $expect): void {
    // Prepare
    $class = new class
    {
        use BetweenFilterable, Filterable, InFilterable, Projectable, Searchable, Sortable;
        use Expandable;
    };

    $method = new ReflectionMethod($class, 'expand');
    $method->setAccessible(true);

    $_GET['expand'] = $expand;

    $alias = current($expand);
    $_GET["{$alias}_fields"] = $fields;

    $_GET["{$alias}_between"] = $betweenFilter;

    // Act & Assert
    expect($method->invoke(null, $expandable))->toBe($expect);
})->with('expandable')
    ->with([
        'all fields' => [
            'expand' => ['posts' => 'p'],
            'fields' => '*',
            'betweenFilter' => ['id' => '1, 10'],

            'expect' => [
                [
                    'relation' => 'posts',
                    'table' => 'posts',
                    'fields' => ['*'],
                    'filter' => [],
                    'inFilter' => [],
                    'betweenFilter' => [
                        ['AND', 'id', false, ['1', '10']],
                    ],
                    'searchFilter' => [],
                    'sort' => [],
                ],
            ],
        ],

        'defined fields' => [
            'expand' => ['posts' => 'p'],
            'fields' => 'id, title',
            'betweenFilter' => ['AND! id' => '1, 10'],

            'expect' => [
                [
                    'relation' => 'posts',
                    'table' => 'posts',
                    'fields' => ['id', 'title', 'user_id'],
                    'filter' => [],
                    'inFilter' => [],
                    'betweenFilter' => [
                        ['AND', 'id', true, ['1', '10']],
                    ],
                    'searchFilter' => [],
                    'sort' => [],
                ],
            ],
        ],
    ]);

test('expand search filter', function (array $expandable, array $expand, string $fields, array $searchFilter, array $expect): void {
    // Prepare
    $class = new class
    {
        use BetweenFilterable, Filterable, InFilterable, Projectable, Searchable, Sortable;
        use Expandable;
    };

    $method = new ReflectionMethod($class, 'expand');
    $method->setAccessible(true);

    $_GET['expand'] = $expand;

    $alias = current($expand);
    $_GET["{$alias}_fields"] = $fields;

    $_GET["{$alias}_search"] = $searchFilter;

    // Act & Assert
    expect($method->invoke(null, $expandable))->toBe($expect);
})->with('expandable')
    ->with([
        'all fields' => [
            'expand' => ['posts' => 'p'],
            'fields' => '*',
            'searchFilter' => ['name' => 'foo'],

            'expect' => [
                [
                    'relation' => 'posts',
                    'table' => 'posts',
                    'fields' => ['*'],
                    'filter' => [],
                    'inFilter' => [],
                    'betweenFilter' => [],
                    'searchFilter' => ['name' => '%foo%'],
                    'sort' => [],
                ],
            ],
        ],

        'defined fields' => [
            'expand' => ['posts' => 'p'],
            'fields' => 'id, title, name',
            'searchFilter' => ['title, name' => '%foo'],

            'expect' => [
                [
                    'relation' => 'posts',
                    'table' => 'posts',
                    'fields' => ['id', 'title', 'name', 'user_id'],
                    'filter' => [],
                    'inFilter' => [],
                    'betweenFilter' => [],
                    'searchFilter' => ['title, name' => '%foo'],
                    'sort' => [],
                ],
            ],
        ],
    ]);

test('expand sort filter', function (array $expandable, array $expand, string $fields, array $sort, array $expect): void {
    // Prepare
    $class = new class
    {
        use BetweenFilterable, Filterable, InFilterable, Projectable, Searchable, Sortable;
        use Expandable;
    };

    $method = new ReflectionMethod($class, 'expand');
    $method->setAccessible(true);

    $_GET['expand'] = $expand;

    $alias = current($expand);
    $_GET["{$alias}_fields"] = $fields;

    $_GET["{$alias}_sort"] = $sort;

    // Act & Assert
    expect($method->invoke(null, $expandable))->toBe($expect);
})->with('expandable')
    ->with([
        'all fields' => [
            'expand' => ['posts' => 'p'],
            'fields' => '*',
            'sort' => ['name' => ''],

            'expect' => [
                [
                    'relation' => 'posts',
                    'table' => 'posts',
                    'fields' => ['*'],
                    'filter' => [],
                    'inFilter' => [],
                    'betweenFilter' => [],
                    'searchFilter' => [],
                    'sort' => ['name' => 'ASC'],
                ],
            ],
        ],

        'defined fields' => [
            'expand' => ['posts' => 'p'],
            'fields' => 'id, name',
            'sort' => ['name' => 'd'],

            'expect' => [
                [
                    'relation' => 'posts',
                    'table' => 'posts',
                    'fields' => ['id', 'name', 'user_id'],
                    'filter' => [],
                    'inFilter' => [],
                    'betweenFilter' => [],
                    'searchFilter' => [],
                    'sort' => ['name' => 'DESC'],
                ],
            ],
        ],
    ]);
