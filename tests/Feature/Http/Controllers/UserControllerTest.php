<?php

use Workbench\App\Models\User;

use function Pest\Laravel\get;

beforeEach(function (): void {
    $_GET = [];
});

test('index no params', function (): void {
    User::factory()->create();

    get('/api/users')
        ->assertOk()
        ->assertJsonStructure([
            '*' => ['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at'],
        ]);
});

test('index fields param', function (): void {
    User::factory()->create();
    $_GET['fields'] = 'id,name,email';

    get('/api/users')
        ->assertOk()
        ->assertJsonStructure([
            '*' => ['id', 'name', 'email'],
        ]);
});

test('index fields! param', function (): void {
    User::factory()->create();
    $_GET['fields'] = 'id,name,email';
    $_GET['fields!'] = 'id';

    get('/api/users')
        ->assertOk()
        ->assertJsonStructure([
            '*' => ['name', 'email'],
        ]);
});

test('index filter param', function (string $operator, string $bool, int $expectCount): void {
    $user = User::factory()->create();

    $_GET['filter'] = ["{$bool}name" => "{$operator}{$user->name}"];

    get('/api/users')
        ->assertOk()
        ->assertJsonCount($expectCount);
})
    ->with([
        ['', '', 1], ['=', '', 1], ['= ', '', 1],
        ['', 'AND! ', 0], ['=', 'AND! ', 0], ['= ', 'AND! ', 0],

        ['>', '', 0], ['> ', '', 0],
        ['>', 'AND! ', 1], ['> ', 'AND! ', 1],

        ['<', '', 0], ['< ', '', 0],
        ['<', 'AND! ', 1], ['< ', 'AND! ', 1],

        ['>=', '', 1], ['>= ', '', 1],
        ['>=', 'AND! ', 0], ['>= ', 'AND! ', 0],

        ['<=', '', 1], ['<= ', '', 1],
        ['<=', 'AND! ', 0], ['<= ', 'AND! ', 0],

        ['<>', '', 0], ['<> ', '', 0],
        ['<>', 'AND! ', 1], ['<> ', 'AND! ', 1],
    ]);

test('index filter param with AND/OR operator', function (string $operator1, string $operator2, string $bool, int $expectCount): void {
    $user = User::factory()->create();

    $_GET['filter'] = [
        'id' => "{$operator1}{$user->id}",
        "{$bool}id" => "{$operator2}".($user->id + 100),
    ];

    get('/api/users')
        ->assertOk()
        ->assertJsonCount($expectCount);
})
    ->with([
        ['', '', 'AND ', 0], ['=', '=', 'AND ', 0], ['= ', '= ', 'AND ', 0],
        ['', '', 'OR ', 1], ['=', '=', 'OR ', 1], ['= ', '= ', 'OR ', 1],

        ['>', '<', 'AND ', 0], ['> ', '< ', 'AND ', 0],
        ['>', '<', 'OR ', 1], ['> ', '< ', 'OR ', 1],

        ['>=', '<=', 'AND ', 1], ['>= ', '<= ', 'AND ', 1],
        ['>=', '<=', 'OR ', 1], ['>= ', '<= ', 'OR ', 1],

        ['<>', '<>', 'AND ', 0], ['<> ', '<> ', 'AND ', 0],
        ['<>', '<>', 'AND ', 0], ['<> ', '<> ', 'AND ', 0],
    ]);

test('index search param', function (string $keyword, int $expectCount): void {
    User::factory()->create(['name' => 'foo']);
    User::factory()->create(['name' => 'bar']);
    User::factory()->create(['name' => 'baz']);

    $_GET['search'] = ['name' => $keyword];

    get('/api/users')
        ->assertOk()
        ->assertJsonCount($expectCount);
})
    ->with([
        ['foo', 1],
        ['ba%', 2],
    ]);

test('index in param', function (string $keyword, int $expectCount): void {
    User::factory()->create(['id' => 18]);
    User::factory()->create(['id' => 21]);
    User::factory()->create(['id' => 60]);
    $_GET['in'] = ['id' => $keyword];

    get('/api/users')
        ->assertOk()
        ->assertJsonCount($expectCount);
})
    ->with([
        ['18', 1],
        ['18, 21', 2],
        ['18, 21, 60', 3],
    ]);

test('index between param', function (string $keyword, int $expectCount): void {
    User::factory()->create(['id' => 18]);
    User::factory()->create(['id' => 21]);
    User::factory()->create(['id' => 60]);
    $_GET['between'] = ['id' => $keyword];

    get('/api/users')
        ->assertOk()
        ->assertJsonCount($expectCount);
})
    ->with([
        ['1, 19', 1],
        ['18, 30', 2],
        ['15, 90', 3],
    ]);

test('index sort ascending param', function ($direction, array $expectJson): void {
    User::factory()->create(['name' => 'foo']);
    User::factory()->create(['name' => 'bar']);
    User::factory()->create(['name' => 'baz']);

    $_GET['sort'] = ['name' => $direction];

    get('/api/users')
        ->assertOk()
        ->assertJson($expectJson);
})
    ->with(['', null])
    ->with([
        'expectJson' => [
            [
                ['name' => 'bar'],
                ['name' => 'baz'],
                ['name' => 'foo'],
            ],
        ],
    ]);

test('index sort descending param', function (string $direction, array $expectJson): void {
    User::factory()->create(['name' => 'foo']);
    User::factory()->create(['name' => 'bar']);
    User::factory()->create(['name' => 'baz']);

    $_GET['sort'] = ['name' => $direction];

    get('/api/users')
        ->assertOk()
        ->assertJson($expectJson);
})
    ->with(['d', 'des', 'desc', 'descending', '-'])
    ->with([
        'expectJson' => [
            [
                ['name' => 'foo'],
                ['name' => 'baz'],
                ['name' => 'bar'],
            ],
        ],
    ]);

test('index limit param', function (int $limit): void {
    User::factory(10)->create();

    $_GET['limit'] = $expectCount = $limit;

    get('/api/users')
        ->assertOk()
        ->assertJsonCount($expectCount);
})
    ->with([3, 5, 7, 9]);

test('index offset param', function (int $offset, int $expectCount): void {
    User::factory(10)->create();

    $_GET['limit'] = 5;
    $_GET['offset'] = $offset;

    get('/api/users')
        ->assertOk()
        ->assertJsonCount($expectCount);
})
    ->with([
        [3, 5],
        [5, 5],
        [7, 3],
        [9, 1],
    ]);

test('index per-page param', function (): void {
    User::factory(10)->create();

    $_GET['per-page'] = 5;

    get('/api/users')
        ->assertOk()
        ->assertJsonStructure([
            'current_page',
            'data',
            'first_page_url',
            // ...
            'total',
        ]);
});

test('index debug param', function (): void {
    $_GET['debug'] = 1;

    get('/api/users')
        ->assertOk()
        ->assertSeeTextInOrder([
            'Array', 'select', 'from', 'users',
        ]);
});

// test('index expand param', function () {
// })->todo();
