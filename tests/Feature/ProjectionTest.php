<?php

use Workbench\App\Models\User;

use function Pest\Laravel\get;

beforeEach(function () {
    $_GET = [];

    User::factory()->create();
});

test('client input fields', function ($clientInputFields, $expectedExactJsonStructure) {
    // Prepare
    $_GET['fields'] = $clientInputFields;

    // Act & Assert
    get('/api/users')
        ->assertOk()
        ->assertExactJsonStructure($expectedExactJsonStructure);
})
    ->with([
        [
            'clientInputFields' => '*',        'expectedExactJsonStructure' => ['*' => ['id', 'name']]
        ],
        [
            'clientInputFields' => 'id',       'expectedExactJsonStructure' => ['*' => ['id']]
        ],
        [
            'clientInputFields' => 'name',     'expectedExactJsonStructure' => ['*' => ['name']]
        ],
        [
            'clientInputFields' => 'id,name',  'expectedExactJsonStructure' => ['*' => ['id', 'name']]
        ],
        [
            'clientInputFields' => 'id, name', 'expectedExactJsonStructure' => ['*' => ['id', 'name']]
        ],
    ]);

test('client input fields!', function ($clientInputFieldsNot, $expectedExactJsonStructure) {
    // Prepare
    $_GET['fields!'] = $clientInputFieldsNot;

    // Act & Assert
    get('/api/users')->dump()
        ->assertOk()
        ->assertExactJsonStructure($expectedExactJsonStructure);
})
    ->with([
        [
            'clientInputFieldsNot' => '*',        'expectedExactJsonStructure' => []
        ],
        // [
        //     'clientInputFieldsNot' => 'id',       'expectedExactJsonStructure' => ['*' => ['id']]
        // ],
        // [
        //     'clientInputFieldsNot' => 'name',     'expectedExactJsonStructure' => ['*' => ['name']]
        // ],
        // [
        //     'clientInputFieldsNot' => 'id,name',  'expectedExactJsonStructure' => ['*' => ['id', 'name']]
        // ],
        // [
        //     'clientInputFieldsNot' => 'id, name', 'expectedExactJsonStructure' => ['*' => ['id', 'name']]
        // ],
    ])->only();
