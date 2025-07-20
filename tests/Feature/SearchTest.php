<?php

use Workbench\App\Models\AllFieldsAreProjectableModel;
use Workbench\App\Models\InvalidProjectableModel;
use Workbench\App\Models\NoProjectableModel;
use Workbench\App\Models\OnlyIdAndNameAreProjectableModel;
use Workbench\App\Models\OnlyIdIsProjectableModel;
use Workbench\App\Models\OnlyNameIsProjectableModel;

use function Pest\Laravel\get;

beforeEach(function (): void {
    $_GET = [];

    $this->data = [
        ['id' => 1, 'name' => 'Mr. Anderson'],
        ['id' => 2, 'name' => 'John Wick'],
        ['id' => 3, 'name' => 'Peter Parker SR.'],
        ['id' => 4, 'name' => 'John Doe JR.'],
        ['id' => 5, 'name' => 'Foo Bar III'],
    ];
});

describe('Not perform any action.', function (): void {
    it('should not perform any action if the search fields is empty', function (): void {
        // Prepare
        $_GET['search'] = ['' => 'Mr%'];
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertExactJson($this->data);
    });

    it('should not perform any action if the search fields is invalid', function (): void {
        // Prepare
        $_GET['search'] = ['email' => 'Mr%']; // email is non-exist column
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertExactJson($this->data);
    });

    it('should not perform any action if the search value is empty', function (): void {
        // Prepare
        $_GET['search'] = ['name' => ''];
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertExactJson($this->data);
    });

    it('should not perform any action if the search value is not hit the minimum', function (): void {
        $minimumLength = config('tweaker.searching.minimum_length');

        // Prepare
        $_GET['search'] = ['name' => str_repeat('a', $minimumLength - 1)];
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertExactJson($this->data);
    });

    it('should not perform any action if the searchable fields are empty', function (): void {
        // Prepare
        $_GET['search'] = ['name' => 'Mr%'];
        NoProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/no-projectable')
            ->assertOk()
            ->assertExactJson($this->data);
    });

    it('should not perform any action if the result key is empty', function (): void {
        // Prepare
        $_GET['search'] = ['name' => 'Mr%'];
        OnlyIdIsProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/only-id-is-projectable')
            ->assertOk()
            ->assertExactJson($this->data);
    });
});

describe('Throw an exception', function (): void {
    it('should throw an exception if one of searchable fields is invalid', function (): void {
        $_GET['search'] = ['name' => 'Mr%'];
        InvalidProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/invalid-projectable')
            ->assertServerError();
    });
});

describe('Valid scenarios', function (): void {
    it('should passed all valid scenarios for client input "fields"', function (
        $searchableFields,
        $search_fields,

        $search_value_no_wildcard,
        $search_value_both_wildcard,
        $search_value_left_wildcard,
        $search_value_right_wildcard,

        $result_fields,

        $result_value_unit_no_wildcard,
        $result_value_unit_both_wildcard,
        $result_value_unit_left_wildcard,
        $result_value_unit_right_wildcard,

        $result_value_feature_no_wildcard,
        $result_value_feature_both_wildcard,
        $result_value_feature_left_wildcard,
        $result_value_feature_right_wildcard,
    ): void {
        // Prepare
        $equivalentRoutes = [
            '*' => '/api/all-fields-are-projectable',
            'id' => '/api/only-id-is-projectable',
            'name' => '/api/only-name-is-projectable',
            'id, name' => '/api/only-id-and-name-are-projectable',
        ];

        $models = [
            '*' => AllFieldsAreProjectableModel::class,
            'id' => OnlyIdIsProjectableModel::class,
            'name' => OnlyNameIsProjectableModel::class,
            'id, name' => OnlyIdAndNameAreProjectableModel::class,
        ];

        $key = implode(', ', $searchableFields);

        $route = $equivalentRoutes[$key];

        $model = $models[$key];
        $model::factory()->createMany($this->data);

        $_GET['search'] = [$search_fields => $search_value_no_wildcard];

        // Act & Assert
        get($route)
            ->assertOk()
            ->assertJsonCount(empty($result_value_feature_no_wildcard) ? 0 : 1);
    })
        ->with('searching-truth-table')->only();
});
