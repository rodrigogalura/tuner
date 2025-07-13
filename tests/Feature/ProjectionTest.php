<?php

use Workbench\App\Models\AllFieldsAreProjectableModel;

use function Pest\Laravel\get;

beforeEach(function () {
    $_GET = [];

    $this->data = AllFieldsAreProjectableModel::factory()->create();
    $this->allFields = ['id', 'name'];
});

describe('Not perform any action', function () {
    beforeEach(function() {
        $this->defaultResponseStructure = $_GET['defined_fields'] = ['name'];
    });

    it('should not perform any action if neither "fields" nor "fields!" is used', function () {
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertJsonCount($this->data->count())
            ->assertExactJsonStructure(['*' => $this->defaultResponseStructure]);
    });

    it('should not perform any action if both "fields" and "fields!" are used', function () {
        $_GET['fields'] = $this->allFields;
        $_GET['fields!'] = $this->allFields;

        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertJsonCount($this->data->count())
            ->assertExactJsonStructure(['*' => $this->defaultResponseStructure]);
    });

    // it('should not perform any action if the client input "fields!" is "*"', function () {
    //     $_GET['fields!'] = '*';

    //     get('/api/all-fields-are-projectable')
    //         ->assertOk()
    //         ->assertJsonCount($this->data->count())
    //         ->assertExactJsonStructure(['*' => $this->defaultResponseStructure]);
    // });

    // it('should not perform any action if the projectable field\'s value is empty', function () {
    //     // Prepare
    //     $projection = new Projection(
    //         $this->model,
    //         projectableFields: [],
    //         definedFields: ['*'],
    //         clientInput: ['fields' => implode(',', $this->visibleFields)],
    //     );

    //     // Act & Assert
    //     expect($projection->handle())->toBeNull();
    // });

    // it('should not perform any action if the projectable fields and defined fields are not intersect', function () {
    //     // Prepare
    //     $projection = new Projection(
    //         $this->model,
    //         projectableFields: [$this->visibleFields[0]],
    //         definedFields: [$this->visibleFields[1]],
    //         clientInput: [],
    //     );

    //     // Act & Assert
    //     expect($projection->handle())->toBeNull();
    // });
});

// test('client input fields', function ($clientInputFields, $expectedExactJsonStructure) {
//     // Prepare
//     $_GET['fields'] = $clientInputFields;

//     // Act & Assert
//     get('/api/users')->dump()
//         ->assertOk()
//         ->assertExactJsonStructure($expectedExactJsonStructure);
// })
//     ->with([
//         [
//             'clientInputFields' => '*',        'expectedExactJsonStructure' => ['*' => ['id', 'name']],
//         ],
//         [
//             'clientInputFields' => 'id',       'expectedExactJsonStructure' => ['*' => ['id']],
//         ],
//         [
//             'clientInputFields' => 'name',     'expectedExactJsonStructure' => ['*' => ['name']],
//         ],
//         [
//             'clientInputFields' => 'id,name',  'expectedExactJsonStructure' => ['*' => ['id', 'name']],
//         ],
//         [
//             'clientInputFields' => 'id, name', 'expectedExactJsonStructure' => ['*' => ['id', 'name']],
//         ],
//         // [
//         //     'clientInputFields' => '', 'expectedExactJsonStructure' => [],
//         // ],
//     ]);

// test('client input fields!', function ($clientInputFieldsNot, $expectedExactJsonStructure) {
//     // Prepare
//     $_GET['fields!'] = $clientInputFieldsNot;

//     // Act & Assert
//     get('/api/users')->dump()
//         ->assertOk()
//         ->assertExactJsonStructure($expectedExactJsonStructure);
// })
//     ->with([
//         // [
//         //     'clientInputFieldsNot' => '*',        'expectedExactJsonStructure' => [],
//         // ],
//         [
//             'clientInputFieldsNot' => 'id',       'expectedExactJsonStructure' => ['*' => ['name']]
//         ],
//         [
//             'clientInputFieldsNot' => 'name',     'expectedExactJsonStructure' => ['*' => ['id']]
//         ],
//         // [
//         //     'clientInputFieldsNot' => 'id,name',  'expectedExactJsonStructure' => []
//         // ],
//         // [
//         //     'clientInputFieldsNot' => 'id, name', 'expectedExactJsonStructure' => []
//         // ],
//     ])->only();
