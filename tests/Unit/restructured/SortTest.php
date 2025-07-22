<?php

use Laradigs\Tweaker\Projection\NoActionWillPerformException;
use Laradigs\Tweaker\Sort\Sort;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;

beforeEach(function (): void {
    $this->visibleFields = ['id', 'name'];
});

describe('Not perform any action.', function (): void {
    it('should not perform any action if the sort input is empty', function (): void {
        // Prepare
        $sort = new Sort(
            $this->visibleFields,
            sortableFields: $this->visibleFields,
            clientInput: ['sort' => []],
        );

        // Act & Assert
        expect(fn () => $sort->sort())->toThrow(NoActionWillPerformException::class);
    });

    it('should not perform any action if the sort input is multi-dimensional array', function (): void {
        // Prepare
        $sort = new Sort(
            $this->visibleFields,
            sortableFields: $this->visibleFields,
            clientInput: ['sort' => ['direction' => ['descending']]],
        );

        // Act & Assert
        expect(fn () => $sort->sort())->toThrow(NoActionWillPerformException::class);
    });

    it('should not perform any action if the sort "fields" are empty', function (): void {
        // Prepare
        $sort = new Sort(
            $this->visibleFields,
            sortableFields: $this->visibleFields,
            clientInput: ['sort' => ['' => 'descending']],
        );

        // Act & Assert
        expect(fn () => $sort->sort())->toThrow(NoActionWillPerformException::class);
    });

    it('should not perform any action if one of sort "fields" is invalid', function (): void {
        // Prepare
        $sort = new Sort(
            $this->visibleFields,
            sortableFields: $this->visibleFields,
            clientInput: ['sort' => ['email' => 'descending']], // 'email' is not existing on visible field]s
        );

        // Act & Assert
        expect(fn () => $sort->sort())->toThrow(NoActionWillPerformException::class);
    });

    it('should not perform any action if the sortable fields are empty', function (): void {
        // Prepare
        $sort = new Sort(
            $this->visibleFields,
            sortableFields: [],
            clientInput: ['sort' => ['name' => '']],
        );

        // Act & Assert
        expect(fn () => $sort->sort())->toThrow(NoActionWillPerformException::class);
    });
});

describe('Throw an exception', function (): void {
    it('should throw an exception if one of sortable fields is invalid', function (): void {
        // Prepare
        $sort = new Sort(
            $this->visibleFields,
            sortableFields: ['email'], // not existing on visible fields
            clientInput: ['sort' => ['name' => 'descending']],
        );

        // Act & Assert
        expect(fn () => $sort->sort())->toThrow(InvalidFieldsException::class);
    });
});

describe('Valid scenarios', function (): void {
    it('should passed all valid scenarios', function ($sortableFields, $clientFields, $resultFields, $clientDirection, $resultDirection): void {
        // Prepare
        $sort = new Sort(
            $this->visibleFields,
            sortableFields: $sortableFields,
            clientInput: ['sort' => array_fill_keys($clientFields, $clientDirection)],
        );

        // Act & Assert
        expect($sort->sort())->toBe(array_fill_keys($resultFields, $resultDirection));
    })
        ->with('sort-fields-truth-table')
        ->with('sort-keyword-truth-table');
})->only();
