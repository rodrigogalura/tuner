<?php

use Laradigs\Tweaker\Sort\Sort;
use Laradigs\Tweaker\DisabledException;
use Illuminate\Validation\ValidationException;
use Laradigs\Tweaker\Sort\InvalidSortableException;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;

DEFINE('SORT_KEY', 'sort');
// DEFINE('SORT_VALID_DIRECTIONS', ['', 'd', 'des', 'desc', 'descending', '-']);

beforeEach(function (): void {
    $this->visibleFields = ['id', 'name'];
});

describe('Prerequisites', function () {
    it('should throw DisabledException if the sortable fields are empty', function ($sortableFields): void {
        // Prepare
        $sort = new Sort(
            $this->visibleFields,
            $sortableFields,
            clientInput: [SORT_KEY => ['id' => 'DESC']],
        );

        // Act & Expect Throws
        $sort->sort();
    })
    ->with(['', null, [[]], false, 0, '0'])
    ->throws(DisabledException::class);

    it('should throw InvalidSortableException if all sortable fields are not in visible fields', function (): void {
        // Prepare
        $notInVisibleFields = ['email'];
        $sort = new Sort(
            $this->visibleFields,
            sortableFields: $notInVisibleFields,
            clientInput: [SORT_KEY => ['id' => 'DESC']],
        );

        // Act & Expect Throws
        $sort->sort();
    })
    ->throws(InvalidSortableException::class);
});

describe('Validations', function () {
    it('should throw ValidationException if the input is not a linear array', function ($input): void {
        // Prepare
        $sort = new Sort(
            $this->visibleFields,
            sortableFields: ['*'],
            clientInput: [SORT_KEY => $input],
        );

        // Act & Expect Throws
        $sort->sort();
    })
    ->with([
        'string' => ['desc'],
        'numeric' => [1],
        'multi-array' =>
            [
                [
                    'id' => ['desc']
                ]
            ],
    ])
    ->throws(ValidationException::class);

    it('should throw ValidationException if the input fields are not in sortable fields', function (): void {
        // Prepare
        $invalidInput = ['email' => 'desc']; // email is not in sortable fields
        $sort = new Sort(
            $this->visibleFields,
            sortableFields: ['id', 'name'],
            clientInput: [SORT_KEY => $invalidInput],
        );

        // Act & Expect Throws
        $sort->sort();
    })
    ->throws(ValidationException::class);

    it('should throw ValidationException if the input direction is not in valid directions', function ($direction): void {
        // Prepare
        $sort = new Sort(
            $this->visibleFields,
            sortableFields: ['id', 'name'],
            clientInput: [SORT_KEY => ['id' => $direction]],
        );

        // Act & Expect Throws
        $sort->sort();
    })
    ->with(['a', 'b', 'c'])
    ->throws(ValidationException::class);
});

describe('Valid scenarios', function (): void {
    it('should passed all valid scenarios', function ($sortableFields, $clientFields, $resultFields, $clientDirection, $resultDirection): void {
        // Prepare
        $sort = new Sort(
            $this->visibleFields,
            sortableFields: $sortableFields,
            clientInput: [SORT_KEY => array_fill_keys($clientFields, $clientDirection)],
        );

        // Act & Assert
        expect($sort->sort())->toBe(array_fill_keys($resultFields, $resultDirection));
    })
        ->with('sort-fields-truth-table')
        ->with('sort-keyword-truth-table');
})->only();
