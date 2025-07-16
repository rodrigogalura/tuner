<?php

use Illuminate\Database\Eloquent\Model;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;
use Laradigs\Tweaker\Searching\Searching;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;

beforeEach(function (): void {
    Mockery::globalHelpers();

    $table = 'users';
    $this->visibleFields = ['id', 'name'];

    $this->model = mock(Model::class);
    $this->model
        ->shouldReceive('getTable')
        ->andReturn($table)
        ->shouldReceive('getHidden')
        ->andReturn([])
        ->shouldReceive('getConnection->getSchemaBuilder->getColumnListing')
        ->with(Mockery::type('string'))
        ->andReturn($this->visibleFields);
});

afterEach(function (): void {
    Mockery::close();
});

describe('Not perform any action. Just return defined value as default.', function (): void {
    it('should not perform any action if the search "fields" are empty', function (): void {
        // Prepare
        $searching = new Searching(
            $this->model,
            searchableFields: $this->visibleFields,
            clientInput: ['' => 'foo bar'],
        );

        // Act & Assert
        expect(fn () => $searching->search())->toThrow(NoActionWillPerformException::class);
    });

    it('should not perform any action if one of search "fields" is invalid', function (): void {
        // Prepare
        $searching = new Searching(
            $this->model,
            searchableFields: $this->visibleFields,
            clientInput: ['email' => 'foo'], // 'email' is not existing on visible fields
        );

        // Act & Assert
        expect(fn () => $searching->search())->toThrow(NoActionWillPerformException::class);
    });

    it('should not perform any action if the search "value" is empty', function (): void {
        // Prepare
        $searching = new Searching(
            $this->model,
            searchableFields: $this->visibleFields,
            clientInput: ['name' => ''],
        );

        // Act & Assert
        expect(fn () => $searching->search())->toThrow(NoActionWillPerformException::class);
    });

    it('should not perform any action if the search "value" length not hit the minimum', function (): void {
        $MINIMUM_LENGTH = 5;
        $searchKeyword = str_repeat('A', $MINIMUM_LENGTH - 1);

        // Prepare
        $searching = new Searching(
            $this->model,
            searchableFields: $this->visibleFields,
            clientInput: ['name' => $searchKeyword],
            minimumLength: $MINIMUM_LENGTH
        );

        // Act & Assert
        expect(fn () => $searching->search())->toThrow(NoActionWillPerformException::class);
    });

    it('should not perform any action if the searchable fields are empty', function (): void {
        // Prepare
        $searching = new Searching(
            $this->model,
            searchableFields: [],
            clientInput: ['name' => 'foo'],
        );

        // Act & Assert
        expect(fn () => $searching->search())->toThrow(NoActionWillPerformException::class);
    });
});

describe('Throw an exception', function (): void {
    it('should throw an exception if one of searchable fields is invalid', function (): void {
        // Prepare
        $searching = new Searching(
            $this->model,
            searchableFields: ['email'], // not existing on visible fields
            clientInput: ['name' => 'foo'],
        );

        // Act & Assert
        expect(fn () => $searching->search())->toThrow(InvalidFieldsException::class);
    });
});

describe('Valid scenarios', function (): void {
    // beforeEach(function() {
    //     $this->datatable = [
    //         ['id' => 1, 'name' => 'Mr. Anderson'],
    //         ['id' => 2, 'name' => 'John Wick'],
    //         ['id' => 3, 'name' => 'Peter Parker SR.'],
    //         ['id' => 4, 'name' => 'John Doe JR.'],
    //         ['id' => 5, 'name' => 'Foo Bar III'],
    //     ];
    // });

    it('should passed all valid scenarios', function (
        $searchableFields,
        $search_fields,
        $search_value_no_wildcard,
        $search_value_both_wildcard,
        $search_value_left_wildcard,
        $search_value_right_wildcard,
        $result_fields,
        $result_value_no_wildcard,
        $result_value_both_wildcard,
        $result_value_left_wildcard,
        $result_value_right_wildcard,
    ): void {
        // Prepare
        $noWildcard = new Searching(
            $this->model,
            searchableFields: $searchableFields,
            clientInput: [$search_fields => $search_value_no_wildcard],
        );

        $bothWildcard = new Searching(
            $this->model,
            searchableFields: $searchableFields,
            clientInput: [$search_fields => $search_value_both_wildcard],
        );

        $leftWildcard = new Searching(
            $this->model,
            searchableFields: $searchableFields,
            clientInput: [$search_fields => $search_value_left_wildcard],
        );

        $rightWildcard = new Searching(
            $this->model,
            searchableFields: $searchableFields,
            clientInput: [$search_fields => $search_value_right_wildcard],
        );

        // Act & Assert
        expect($noWildcard->search())->toBe([$result_fields => $result_value_no_wildcard]);
        expect($bothWildcard->search())->toBe([$result_fields => $result_value_both_wildcard]);
        expect($leftWildcard->search())->toBe([$result_fields => $result_value_left_wildcard]);
        expect($rightWildcard->search())->toBe([$result_fields => $result_value_right_wildcard]);
    })
        ->with('searching-truth-table');
});
