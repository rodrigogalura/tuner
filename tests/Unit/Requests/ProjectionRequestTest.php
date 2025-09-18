<?php

use Tuner\Columns\DefinedColumns;
use Tuner\Columns\ProjectableColumns;
use Tuner\Exceptions\ClientException;
use Tuner\Exceptions\TunerException;
use Tuner\Requests\ProjectionRequest;

describe('Projection Request', function (): void {
    it('should thrown an exception when projectable columns are empty.', function (): void {
        // Prepare
        $config = [
            'key' => [
                'intersect' => 'columns',
                'except' => 'columns!',
            ],
        ];

        $request = ['columns' => 1];

        // Act & Assert
        new ProjectionRequest($config, $request, visibleColumns: ['foo'], projectableColumns: [], definedColumns: ['*']);
    })->throws(
        TunerException::class,
        exceptionCode: ProjectableColumns::ERR_CODE_DISABLED
    );

    it('should thrown an exception when all projectable columns are not in visible columns.', function (): void {
        // Prepare
        $config = [
            'key' => [
                'intersect' => 'columns',
                'except' => 'columns!',
            ],
        ];

        $request = ['columns' => 1];

        // Act & Assert
        new ProjectionRequest($config, $request, visibleColumns: ['foo', 'bar'], projectableColumns: ['baz'], definedColumns: ['*']);
    })->throws(
        TunerException::class,
        exceptionCode: ProjectableColumns::ERR_CODE_PCOLS_VCOLS_NO_MATCH
    );

    it('should thrown an exception when defined columns are empty.', function (): void {
        // Prepare
        $config = [
            'key' => [
                'intersect' => 'columns',
                'except' => 'columns!',
            ],
        ];

        $request = ['columns' => 1];

        // Act & Assert
        new ProjectionRequest($config, $request, visibleColumns: ['foo'], projectableColumns: ['foo'], definedColumns: []);
    })->throws(
        TunerException::class,
        exceptionCode: DefinedColumns::ERR_CODE_QUERY_EXCEPTION
    );

    it('should thrown an exception when all defined columns are not in visible columns.', function (): void {
        // Prepare
        $config = [
            'key' => [
                'intersect' => 'columns',
                'except' => 'columns!',
            ],
        ];

        $request = ['columns' => 1];

        // Act & Assert
        new ProjectionRequest($config, $request, visibleColumns: ['foo', 'bar'], projectableColumns: ['*'], definedColumns: ['baz']);
    })->throws(
        TunerException::class,
        exceptionCode: DefinedColumns::ERR_CODE_DCOLS_VCOLS_NO_MATCH
    );

    it('should thrown an exception when using modifier for intersect and except at the same time.', function (): void {
        // Prepare
        $config = [
            'key' => [
                'intersect' => 'columns',
                'except' => 'columns!',
            ],
        ];

        $request = [
            'columns' => 'foo',
            'columns!' => 'bar',
        ];

        // Act & Assert
        new ProjectionRequest($config, $request, visibleColumns: [], projectableColumns: [], definedColumns: []);
    })->throws(ClientException::class);

    it('should thrown an exception when request value is not string.', function (): void {
        // Prepare
        $config = [
            'key' => [
                'intersect' => 'columns',
                'except' => 'columns!',
            ],
        ];

        $request = ['columns' => 1];

        // Act & Assert
        new ProjectionRequest($config, $request, visibleColumns: ['foo'], projectableColumns: ['foo'], definedColumns: ['*']);
    })->throws(ClientException::class);

    it('should thrown an exception when requesting non-existing columns.', function (): void {
        // Prepare
        $config = [
            'key' => [
                'intersect' => 'columns',
                'except' => 'columns!',
            ],
        ];

        $request = ['columns' => 'baz'];

        // Act & Assert
        new ProjectionRequest($config, $request, visibleColumns: ['foo', 'bar'], projectableColumns: ['*'], definedColumns: ['*']);
    })
        ->throws(ClientException::class);

    test('should get request value of columns modifier', function (): void {
        // Prepare
        $config = [
            'key' => [
                'intersect' => 'columns',
                'except' => 'columns!',
            ],
        ];

        $request = ['columns' => 'baz'];

        // Act & Assert
        $request = new ProjectionRequest($config, $request, visibleColumns: ['foo', 'bar', 'baz'], projectableColumns: ['*'], definedColumns: ['*']);
        expect($request())->toBe(['columns' => ['baz']]);
    });

    test('should get request value of columns! modifier', function (): void {
        // Prepare
        $config = [
            'key' => [
                'intersect' => 'columns',
                'except' => 'columns!',
            ],
        ];

        $request = ['columns!' => 'baz'];

        // Act & Assert
        $request = new ProjectionRequest($config, $request, visibleColumns: ['foo', 'bar', 'baz'], projectableColumns: ['*'], definedColumns: ['*']);
        expect($request())->toBe(['columns!' => ['foo', 'bar']]);
    });
});
