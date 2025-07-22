<?php

use Workbench\App\Models\AllFieldsAreProjectableModel;
use Workbench\App\Models\NoProjectableModel;
use Workbench\App\Models\OnlyIdAndNameAreProjectableModel;
use Workbench\App\Models\OnlyIdIsProjectableModel;
use Workbench\App\Models\OnlyNameIsProjectableModel;

use function Pest\Laravel\get;

beforeEach(function (): void {
    $_GET = [];
});

describe('Not perform any action.', function (): void {
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
    it('should passed all valid scenarios', function ($projectableFields, $definedFields, $clientInput, $intersectResult, $exceptResult): void {
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

        $_GET['defined_fields'] = $definedFields;

        $model = $models[$key];
        $data = $model::factory(rand(2, 5))->create();

        $route = $equivalentRoutes[$key];

        $_GET['fields'] = $clientInput;

        // Act & Assert
        get($route)
            ->assertOk()
            ->assertJsonCount(empty($intersectResult) ? 0 : $data->count())
            ->assertExactJsonStructure(['*' => $intersectResult]);

        unset($_GET['fields']);
        $_GET['fields!'] = $clientInput;

        // Act & Assert
        get($route)
            ->assertOk()
            ->assertJsonCount(empty($exceptResult) ? 0 : $data->count())
            ->assertExactJsonStructure(['*' => $exceptResult]);
    })
        ->with('projection-truth-table');
});
