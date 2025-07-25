<?php

use Illuminate\Validation\ValidationException;
use Laradigs\Tweaker\DisabledException;
use Laradigs\Tweaker\Sort\InvalidSortableException;
use Laradigs\Tweaker\Sort\Sort;

define('SORT_KEY', 'sort');
define('SORT_VALID_DIRECTIONS', ['', 'd', 'des', 'desc', 'descending', '-']);

beforeEach(function (): void {
    $this->visibleFields = ['id', 'name'];
});

describe('Prerequisites', function (): void {
    it('should throw DisabledException if the sortable fields are empty', function ($sortableFields, $direction): void {
        // Prepare
        $sort = new Sort(
            $this->visibleFields,
            $sortableFields,
            clientInput: [SORT_KEY => ['id' => $direction]],
        );

        // Act & Expect Throws
        $sort->sort();
    })
        ->with(['', null, [[]], false, 0, '0'])
        ->with(SORT_VALID_DIRECTIONS)
        ->throws(DisabledException::class);

    it('should throw InvalidSortableException if all sortable fields are not in visible fields', function ($direction): void {
        // Prepare
        $notInVisibleFields = ['email'];
        $sort = new Sort(
            $this->visibleFields,
            sortableFields: $notInVisibleFields,
            clientInput: [SORT_KEY => ['id' => $direction]],
        );

        // Act & Expect Throws
        $sort->sort();
    })
        ->with(SORT_VALID_DIRECTIONS)
        ->throws(InvalidSortableException::class);
});

describe('Validations', function (): void {
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
            'multi-array' => [
                [
                    'id' => ['desc'],
                ],
            ],
        ])
        ->throws(ValidationException::class);

    it('should throw ValidationException if the input fields are not in sortable fields', function ($direction): void {
        // Prepare
        $sort = new Sort(
            $this->visibleFields,
            sortableFields: ['id', 'name'],
            clientInput: [SORT_KEY => ['email' => $direction]], // email is not in sortable fields
        );

        // Act & Expect Throws
        $sort->sort();
    })
        ->with(SORT_VALID_DIRECTIONS)
        ->throws(ValidationException::class);

    it('should throw ValidationException if the input direction is not in valid directions', function (): void {
        $INVALID_DIRECTION = generateUniqueWord(SORT_VALID_DIRECTIONS, rand(1, 10));

        // Prepare
        $sort = new Sort(
            $this->visibleFields,
            sortableFields: ['id', 'name'],
            clientInput: [SORT_KEY => ['id' => $INVALID_DIRECTION]],
        );

        // Act & Expect Throws
        $sort->sort();
    })
        ->repeat(10)
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
        ->with('sort-direction-truth-table');
});
