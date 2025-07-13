<?php

use function Pest\Laravel\get;
use Workbench\App\Models\NoProjectableModel;
use Workbench\App\Models\OnlyIdIsProjectableModel;
use Workbench\App\Models\AllFieldsAreProjectableModel;

beforeEach(function () {
    $_GET = [];

    $this->visibleFields = ['id', 'name', 'created_at', 'updated_at'];

    // $this->equivalentRoutes = [
    //     '*' => '/api/all-fields-are-projectable',
    //     'id' => '/api/only-id-is-projectable',
    //     'name' => '/api/only-name-is-projectable',
    //     'id, name' => '/api/id-and-name-are-projectable',
    //     'empty' => '/api/no-projectable',
    // ];
});

describe('Not perform any action. Just return defined value as default.', function () {
    it('should not perform any action if the client input "fields!" is "*"', function () {
        // Prepare
        $_GET['fields!'] = '*';
        $_GET['defined_fields'] = ['id', 'name'];
        $data = AllFieldsAreProjectableModel::factory()->create();

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertJsonCount($data->count())
            ->assertExactJsonStructure(['*' => $_GET['defined_fields']]);
    });

    it('should not perform any action if the projectable field\'s value is empty', function () {
        // Prepare
        $_GET['fields!'] = '*';
        $_GET['defined_fields'] = ['id', 'name'];
        $data = NoProjectableModel::factory()->create();

        // Act & Assert
        get('/api/no-projectable')
            ->assertOk()
            ->assertJsonCount($data->count())
            ->assertExactJsonStructure(['*' => $_GET['defined_fields']]);
    });

    it('should not perform any action if the projectable fields and defined fields are not intersect', function () {
        // Prepare
        $_GET['defined_fields'] = ['name'];
        $data = OnlyIdIsProjectableModel::factory()->create();

        // Act & Assert
        get('/api/only-id-is-projectable')
            ->assertOk()
            ->assertJsonCount($data->count())
            ->assertExactJsonStructure(['*' => $_GET['defined_fields']]);
    });
});

describe('Not perform any action', function () {
    // beforeEach(function () {
    //     $this->defaultResponseStructure = $_GET['defined_fields'] = ['name'];
    // });

    // it('should not perform any action if neither "fields" nor "fields!" is used', function () {
    //     get('/api/all-fields-are-projectable')
    //         ->assertOk()
    //         ->assertJsonCount($this->data->count())
    //         ->assertExactJsonStructure(['*' => $this->defaultResponseStructure]);
    // });

    // it('should not perform any action if both "fields" and "fields!" are used', function () {
    //     $_GET['fields'] = $this->allFields;
    //     $_GET['fields!'] = $this->allFields;

    //     get('/api/all-fields-are-projectable')
    //         ->assertOk()
    //         ->assertJsonCount($this->data->count())
    //         ->assertExactJsonStructure(['*' => $this->defaultResponseStructure]);
    // });

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
