<?php

use Workbench\App\Models\AllFieldsAreProjectableModel;
use Workbench\App\Models\NoProjectableModel;
use Workbench\App\Models\OnlyIdAndNameAreProjectableModel;
use Workbench\App\Models\OnlyIdIsProjectableModel;
use Workbench\App\Models\OnlyNameIsProjectableModel;

use function Pest\Laravel\get;

beforeEach(function (): void {
    $_GET = [];

    // $this->equivalentRoutes = [
    //     '*' => '/api/all-fields-are-projectable',
    //     'id' => '/api/only-id-is-projectable',
    //     'name' => '/api/only-name-is-projectable',
    //     'id, name' => '/api/id-and-name-are-projectable',
    //     'empty' => '/api/no-projectable',
    // ];
});

describe('Not perform any action.', function (): void {
    it('should not perform any action if the client input "fields!" is "*"', function (): void {
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

    it('should not perform any action if the projectable field\'s value is empty', function (): void {
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

    it('should not perform any action if the projectable fields and defined fields are not intersect', function (): void {
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

describe('Throw an exception', function (): void {
    it('should throw an exception if one of projectable fields is invalid', function (): void {
        $_GET['fields'] = '*';

        // Act & Assert
        get('/api/invalid-projectable')
            ->assertServerError();
    });

    it('should throw an exception if the defined fields is empty', function (): void {
        $_GET['fields'] = '*';
        $_GET['defined_fields'] = [];

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertServerError();
    });

    it('should throw an exception if one of defined fields is invalid', function (): void {
        $_GET['fields'] = '*';
        $_GET['defined_fields'] = ['email'];

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertServerError();
    });
});

describe('Valid scenarios', function (): void {
    it('should passed all valid scenarios for client input "fields"', function ($projectableFields, $definedFields, $clientInput, $expectedResult): void {
        // Prepare
        $equivalentRoutes = [
            '*' => '/api/all-fields-are-projectable',
            'id' => '/api/only-id-is-projectable',
            'name' => '/api/only-name-is-projectable',
            'id, name' => '/api/only-id-and-name-are-projectable',
            'empty' => '/api/no-projectable',
        ];

        $models = [
            '*' => AllFieldsAreProjectableModel::class,
            'id' => OnlyIdIsProjectableModel::class,
            'name' => OnlyNameIsProjectableModel::class,
            'id, name' => OnlyIdAndNameAreProjectableModel::class,
            'empty' => NoProjectableModel::class,
        ];

        $key = implode(', ', $projectableFields);

        $_GET['fields'] = $clientInput;
        $_GET['defined_fields'] = $definedFields;

        $model = $models[$key];
        $data = $model::factory(rand(2, 5))->create();

        $route = $equivalentRoutes[$key];

        // Act & Assert
        get($route)
            ->assertOk()
            ->assertJsonCount(empty($expectedResult) ? 0 : $data->count())
            ->assertExactJsonStructure(['*' => $expectedResult]);
    })
        ->with('fields-truth-table');

    it('should passed all valid scenarios for client input "fields!"', function ($projectableFields, $definedFields, $clientInput, $expectedResult): void {
        // Prepare
        $equivalentRoutes = [
            '*' => '/api/all-fields-are-projectable',
            'id' => '/api/only-id-is-projectable',
            'name' => '/api/only-name-is-projectable',
            'id, name' => '/api/only-id-and-name-are-projectable',
            'empty' => '/api/no-projectable',
        ];

        $models = [
            '*' => AllFieldsAreProjectableModel::class,
            'id' => OnlyIdIsProjectableModel::class,
            'name' => OnlyNameIsProjectableModel::class,
            'id, name' => OnlyIdAndNameAreProjectableModel::class,
            'empty' => NoProjectableModel::class,
        ];

        $key = implode(', ', $projectableFields);

        $_GET['fields!'] = $clientInput;
        $_GET['defined_fields'] = $definedFields;

        $model = $models[$key];
        $data = $model::factory(rand(2, 5))->create();

        $route = $equivalentRoutes[$key];

        // Act & Assert
        get($route)
            ->assertOk()
            ->assertJsonCount(empty($expectedResult) ? 0 : $data->count())
            ->assertExactJsonStructure(['*' => $expectedResult]);
    })
        ->with('fields-not-truth-table');
});
