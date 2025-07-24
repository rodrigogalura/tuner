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
    it('should not perform any action if the search input is empty', function (): void {
        // Prepare
        $_GET['search'] = [];
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertExactJson($this->data); // same data, search filter not used
    });

    it('should not perform any action if the search input is multi-dimensional array', function (): void {
        // Prepare
        $_GET['search'] = ['name' => ['foo']];
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertExactJson($this->data); // same data, search filter not used
    });

    it('should not perform any action if the search fields is empty', function (): void {
        // Prepare
        $_GET['search'] = ['' => 'Mr%'];
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertExactJson($this->data); // same data, search filter not used
    });

    it('should not perform any action if the search fields is invalid', function (): void {
        // Prepare
        $_GET['search'] = ['email' => 'Mr%']; // email is non-exist column
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertExactJson($this->data); // same data, search filter not used
    });

    it('should not perform any action if the search value is empty', function (): void {
        // Prepare
        $_GET['search'] = ['name' => ''];
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertExactJson($this->data); // same data, search filter not used
    });

    it('should not perform any action if the search value is not hit the minimum', function (): void {
        $minimumLength = config('tweaker.search.minimum_length');

        // Prepare
        $_GET['search'] = ['name' => str_repeat('a', $minimumLength - 1)];
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertExactJson($this->data); // same data, search filter not used
    });

    it('should not perform any action if the searchable fields are empty', function (): void {
        // Prepare
        $_GET['search'] = ['name' => 'Mr%'];
        NoProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/no-projectable')
            ->assertOk()
            ->assertExactJson($this->data); // same data, search filter not used
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
    it('should passed all valid scenarios for all searchable fields', function ($clientKeyword, $expectedCount): void {
        // Prepare
        AllFieldsAreProjectableModel::factory()->createMany($this->data);

        $_GET['search'] = ['name' => $clientKeyword];

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertOk()
            ->assertJsonCount($expectedCount);
    })
        ->with('keyword-and-count');

    it('should passed all valid scenarios for searchable field name', function ($clientKeyword, $expectedCount): void {
        // Prepare
        OnlyNameIsProjectableModel::factory()->createMany($this->data);

        $_GET['search'] = ['name' => $clientKeyword];

        // Act & Assert
        get('/api/only-name-is-projectable')
            ->assertOk()
            ->assertJsonCount($expectedCount);
    })
        ->with('keyword-and-count');

    it('should passed all valid scenarios for searchable fields id and name', function ($clientKeyword, $expectedCount): void {
        // Prepare
        OnlyIdAndNameAreProjectableModel::factory()->createMany($this->data);

        $_GET['search'] = ['name' => $clientKeyword];

        // Act & Assert
        get('/api/only-id-and-name-are-projectable')
            ->assertOk()
            ->assertJsonCount($expectedCount);
    })
        ->with('keyword-and-count');
});
