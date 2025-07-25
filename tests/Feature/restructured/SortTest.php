<?php

use Workbench\App\Models\NoProjectableModel;
use Workbench\App\Models\OnlyIdIsProjectableModel;

use function Pest\Laravel\get;

define('SORT_KEY', 'sort');
define('SORT_ASC_DIRECTIONS', ['']);
define('SORT_DESC_DIRECTIONS', ['d', 'des', 'desc', 'descending', '-']);

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

describe('Prerequisites', function (): void {
    it('should not perform sort', function ($direction): void {
        // Prepare
        $_GET[SORT_KEY] = ['id' => $direction];
        NoProjectableModel::factory()->createMany($this->data);

        // Act & Assert
        get('/api/no-projectable')
            ->assertOk()
            ->assertExactJson($this->data); // same order of data, sort feature not trigger
    })
        ->with(SORT_DESC_DIRECTIONS);

    it('should throw InvalidSortableException if one of sortable field is invalid', function ($direction): void {
        $_GET[SORT_KEY] = ['id' => $direction];

        // Act & Assert
        get('/api/invalid-projectable')
            ->assertServerError();
    })
        ->with(SORT_DESC_DIRECTIONS);
});

describe('Validations', function (): void {
    it('should throw ValidationException if the sort input is multi-dimensional array', function ($direction): void {
        $_GET[SORT_KEY] = ['id' => [$direction]];

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertUnprocessable();
    })
        ->with(SORT_DESC_DIRECTIONS);

    it('should throw ValidationException if one of the sort input fields are not in sortable fields', function ($direction): void {
        $_GET[SORT_KEY] = ['email' => $direction]; // email is not in sortable fields

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertUnprocessable();
    })
        ->with(SORT_DESC_DIRECTIONS);

    it('should throw ValidationException if the sort input direction is not a valid direction', function ($direction): void {
        $INVALID_DIRECTION = generateUniqueWord([$direction], rand(1, 10));
        $_GET[SORT_KEY] = ['id' => $INVALID_DIRECTION];

        // Act & Assert
        get('/api/all-fields-are-projectable')
            ->assertUnprocessable();
    })
        ->with(SORT_DESC_DIRECTIONS)
        ->repeat(10);
});

describe('Valid scenarios', function (): void {
    it('should passed all valid scenarios for ascending', function ($direction): void {
        // Prepare
        OnlyIdIsProjectableModel::factory()->createMany($this->data);

        $ascendingData = OnlyIdIsProjectableModel::orderBy('id', 'ASC')->get()->toArray();

        $_GET[SORT_KEY] = ['id' => $direction];

        // Act & Assert
        get('/api/only-id-is-projectable')
            ->assertOk()
            ->assertExactJson($ascendingData);
    })
        ->with(SORT_ASC_DIRECTIONS);

    it('should passed all valid scenarios for descending', function ($direction): void {
        // Prepare
        OnlyIdIsProjectableModel::factory()->createMany($this->data);

        $descendingData = OnlyIdIsProjectableModel::orderByDesc('id')->get()->toArray();

        $_GET[SORT_KEY] = ['id' => $direction];

        // Act & Assert
        get('/api/only-id-is-projectable')
            ->assertOk()
            ->assertExactJson($descendingData);
    })
        ->with(SORT_DESC_DIRECTIONS);
});
