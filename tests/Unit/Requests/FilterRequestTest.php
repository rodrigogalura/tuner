<?php

use Tuner\Requests\FilterRequest;
use Tuner\Exceptions\TunerException;
use Tuner\Exceptions\ClientException;

describe('Filter Request', function (): void {
    it('should thrown an exception when filterable columns are empty.', function (): void {
        // Prepare
        $config = ['key' => array_combine($keys = ['filter', 'in', 'between'], $keys)];
        $request = ['filter' => ['foo' => 'fooVal']];

        // Act & Assert
        new FilterRequest($config, $request, visibleColumns: ['foo'], filterableColumns: []);
    })->throws(TunerException::class);

    it('should thrown an exception when all filterable columns are not in visible columns.', function (): void {
        // Prepare
        $config = ['key' => array_combine($keys = ['filter', 'in', 'between'], $keys)];
        $request = ['filter' => ['foo' => 'fooVal']];

        // Act & Assert
        new FilterRequest($config, $request, visibleColumns: ['foo', 'bar'], filterableColumns: ['baz']);
    })->throws(TunerException::class);

    it('should thrown an exception when request value is not array.', function ($requestValue): void {
        // Prepare
        $config = ['key' => array_combine($keys = ['filter', 'in', 'between'], $keys)];
        $request = ['filter' => $requestValue];

        // Act & Assert
        new FilterRequest($config, $request, visibleColumns: ['foo'], filterableColumns: ['*']);
    })
        ->with([1, 'foo'])
        ->throws(ClientException::class);

    it('should thrown an exception when logic columns are invalid.', function ($requestValue): void {
        // Prepare
        $config = ['key' => array_combine($keys = ['filter', 'in', 'between'], $keys)];
        $request = ['filter' => $requestValue];

        // Act & Assert
        new FilterRequest($config, $request, visibleColumns: ['foo', 'bar'], filterableColumns: ['*']);
    })
        ->with([
            ['requestValue' => ['foo and bar' => 'foobar']],
            ['requestValue' => ['foo or bar' => 'foobar']],
        ])
        ->throws(ClientException::class);

    it('should thrown an exception when requesting non-existing columns.', function (): void {
        // Prepare
        $config = ['key' => array_combine($keys = ['filter', 'in', 'between'], $keys)];
        $request = ['filter' => ['baz' => 'asc']];

        // Act & Assert
        new FilterRequest($config, $request, visibleColumns: ['foo', 'bar'], filterableColumns: ['*']);
    })
        ->throws(ClientException::class);

    test('should get request value of filter modifier', function (): void {
        // Prepare
        $config = ['key' => array_combine($keys = ['filter', 'in', 'between'], $keys)];
        $request = ['filter' => ['baz' => 'bazVal']];

        // Act & Assert
        $request = new FilterRequest($config, $request, visibleColumns: ['foo', 'bar', 'baz'], filterableColumns: ['*']);
        expect($request())->toBe([
            'filter' => [
                ['AND', 'baz', false, '=', 'bazVal'],
            ],
        ]);
    });
});
