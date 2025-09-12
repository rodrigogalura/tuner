<?php

use Tuner\Tuner\V33\ValueObjects\Requests\FilterRequest;

describe('Sort Request', function (): void {
    it('should thrown an exception when filterable columns are empty.', function (): void {
        // Prepare
        $config = [
            'key' => array_combine($keys = ['filter', 'in', 'between'], $keys),
        ];

        $request = [
            'filter' => ['foo' => 'fooVal'],
        ];

        $visibleColumns = ['foo'];
        $filterableColumns = [];

        // Act & Assert
        new FilterRequest($config, $request, $visibleColumns, $filterableColumns);
    })->throws(Exception::class);

    it('should thrown an exception when all filterable columns are not in visible columns.', function (): void {
        // Prepare
        $config = [
            'key' => array_combine($keys = ['filter', 'in', 'between'], $keys),
        ];

        $request = [
            'filter' => ['foo' => 'fooVal'],
        ];

        $visibleColumns = ['foo', 'bar'];
        $filterableColumns = ['baz'];

        // Act & Assert
        new FilterRequest($config, $request, $visibleColumns, $filterableColumns);
    })->throws(Exception::class);

    it('should thrown an exception when request value is not array.', function ($requestValue): void {
        // Prepare
        $config = [
            'key' => array_combine($keys = ['filter', 'in', 'between'], $keys),
        ];

        $request = [
            'filter' => $requestValue,
        ];

        // Act & Assert
        new FilterRequest($config, $request, ['foo'], ['*']);
    })
        ->with([1, 'foo'])
        ->throws(Exception::class);

    it('should thrown an exception when logic columns are invalid.', function ($requestValue): void {
        // Prepare
        $config = [
            'key' => array_combine($keys = ['filter', 'in', 'between'], $keys),
        ];

        $request = [
            'filter' => $requestValue,
        ];

        $visibleColumns = ['foo', 'bar'];
        $filterableColumns = ['*'];

        // Act & Assert
        new FilterRequest($config, $request, $visibleColumns, $filterableColumns);
    })
        ->with([
            ['requestValue' => ['foo and bar' => 'foobar']],
            ['requestValue' => ['foo or bar' => 'foobar']],
        ])
        ->throws(Exception::class);

    it('should thrown an exception when requesting non-existing columns.', function (): void {
        // Prepare
        $config = [
            'key' => array_combine($keys = ['filter', 'in', 'between'], $keys),
        ];

        $request = [
            'filter' => ['baz' => 'asc'],
        ];

        $visibleColumns = ['foo', 'bar'];
        $filterableColumns = ['*'];

        // Act & Assert
        new FilterRequest($config, $request, $visibleColumns, $filterableColumns);
    })
        ->throws(Exception::class);

    test('should get request value of filter modifier', function (): void {
        // Prepare
        $config = [
            'key' => array_combine($keys = ['filter', 'in', 'between'], $keys),
        ];

        $request = [
            'filter' => ['baz' => 'bazVal'],
        ];

        $visibleColumns = ['foo', 'bar', 'baz'];
        $filterableColumns = ['*'];

        // Act & Assert
        $request = new FilterRequest($config, $request, $visibleColumns, $filterableColumns);
        expect($request())->toBe([
            'filter' => [
                ['AND', 'baz', false, '=', 'bazVal'],
            ],
        ]);
    });
});
