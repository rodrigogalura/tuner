<?php

use Workbench\App\Models\AllFieldsAreProjectableModel;
use Workbench\App\Models\InvalidProjectableModel;
use Workbench\App\Models\NoProjectableModel;
use Workbench\App\Models\OnlyIdAndNameAreProjectableModel;
use Workbench\App\Models\OnlyNameIsProjectableModel;

use function Pest\Laravel\get;

dataset('keyword-and-count', [
    ['clientKeyword' => 'Mr*', 'expectedCount' => 2],
    ['clientKeyword' => '*JR.', 'expectedCount' => 3],
    ['clientKeyword' => 'Bar', 'expectedCount' => 4],
    ['clientKeyword' => '*Bar*', 'expectedCount' => 4],
]);

beforeEach(function (): void {
    $_GET = [];

    // 2 Mr*
    // 3 *JR.
    // 4 *Bar*
    // 4 Bar
    $this->data = [
        ['id' => 1, 'name' => 'Mr. Anderson'],
        ['id' => 2, 'name' => 'Mr. Anna'],
        ['id' => 3, 'name' => 'John Wick JR.'],
        ['id' => 4, 'name' => 'Foo Greenfelder JR.'],
        ['id' => 5, 'name' => 'Alan Doe JR.'],
        ['id' => 6, 'name' => 'Angel Bar Abshire'],
        ['id' => 7, 'name' => 'Peter Bar Pan'],
        ['id' => 8, 'name' => 'Camille Bar McClure'],
        ['id' => 9, 'name' => 'Alanis Bar III'],
    ];
});

describe('Not perform any action.', function (): void {
    it('should not perform any action if the sort input is empty', function (): void {
        // Prepare
        $_GET['sort'] = [];
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertExactJson($this->data); // same order of data, sort feature not trigger
    });

    it('should not perform any action if the sort input is multi-dimensional array', function (): void {
        // Prepare
        $_GET['sort'] = ['name' => ['foo']];
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertExactJson($this->data); // same order of data, sort feature not trigger
    });

    it('should not perform any action if the sort fields is empty', function (): void {
        // Prepare
        $_GET['sort'] = ['' => 'desc'];
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertExactJson($this->data); // same order of data, sort feature not trigger
    });

    it('should not perform any action if the sort fields is invalid', function (): void {
        // Prepare
        $_GET['sort'] = ['email' => 'desc']; // email is non-exist column
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertExactJson($this->data); // same order of data, sort feature not trigger
    });

    it('should not perform any action if the sortable fields are empty', function (): void {
        // Prepare
        $_GET['sort'] = ['id' => 'desc'];
        NoProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/no-projectable')
            ->assertOk()
            ->assertExactJson($this->data); // same order of data, sort feature not trigger
    });
});

describe('Throw an exception', function (): void {
    it('should throw an exception if one of sortable fields is invalid', function (): void {
        $_GET['sort'] = ['id' => 'desc'];
        InvalidProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/invalid-projectable')
            ->assertServerError();
    });
});

// dd(collect($this->data)->sortByDesc('name'));

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
