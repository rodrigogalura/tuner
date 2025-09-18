<?php

use Tuner\Requests\SortRequest;

describe('Sort Request', function (): void {
    it('should thrown an exception when sortable columns are empty.', function (): void {
        // Prepare
        $config = [
            'key' => 'sort',
        ];

        $request = ['sort' => ['foo' => 'asc']];

        // Act & Assert
        new SortRequest($config, $request, visibleColumns: ['foo'], sortableColumns: []);
    })->throws(Exception::class);

    it('should thrown an exception when all sortable columns are not in visible columns.', function (): void {
        // Prepare
        $config = [
            'key' => 'sort',
        ];

        $request = ['sort' => ['foo' => 'asc']];

        // Act & Assert
        new SortRequest($config, $request, visibleColumns: ['foo', 'bar'], sortableColumns: ['baz']);
    })->throws(Exception::class);

    it('should thrown an exception when request value is not array.', function ($requestValue): void {
        // Prepare
        $config = [
            'key' => 'sort',
        ];

        $request = ['sort' => $requestValue];

        // Act & Assert
        new SortRequest($config, $request, ['foo'], ['*']);
    })
        ->with([1, 'foo'])
        ->throws(Exception::class);

    it('should thrown an exception when requesting non-existing columns.', function (): void {
        // Prepare
        $config = [
            'key' => 'sort',
        ];

        $request = ['sort' => ['baz' => 'asc']];

        // Act & Assert
        new SortRequest($config, $request, visibleColumns: ['foo', 'bar'], sortableColumns: ['*']);
    })
        ->throws(Exception::class);

    test('should get request value of sort modifier', function (): void {
        // Prepare
        $config = [
            'key' => 'sort',
        ];

        $request = ['sort' => ['baz' => 'asc']];

        // Act & Assert
        $request = new SortRequest($config, $request, visibleColumns: ['foo', 'bar', 'baz'], sortableColumns: ['*']);
        expect($request())->toBe(['sort' => ['baz' => 'asc']]);
    });
});
