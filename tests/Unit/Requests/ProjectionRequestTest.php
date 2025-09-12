<?php

use Tuner\Requests\ProjectionRequest;

describe('Projection Request', function (): void {
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
        new ProjectionRequest($config, $request, [], [], []);
    })->throws(Exception::class);

    it('should thrown an exception when projectable columns are empty.', function (): void {
        // Prepare
        $config = [
            'key' => [
                'intersect' => 'columns',
                'except' => 'columns!',
            ],
        ];

        $request = [
            'columns' => 1,
        ];

        $visibleColumns = ['foo'];
        $projectableColumns = [];
        $definedColumns = ['*'];

        // Act & Assert
        new ProjectionRequest($config, $request, $visibleColumns, $projectableColumns, $definedColumns);
    })->throws(Exception::class);

    it('should thrown an exception when all projectable columns are not in visible columns.', function (): void {
        // Prepare
        $config = [
            'key' => [
                'intersect' => 'columns',
                'except' => 'columns!',
            ],
        ];

        $request = [
            'columns' => 1,
        ];

        $visibleColumns = ['foo', 'bar'];
        $projectableColumns = ['baz'];
        $definedColumns = ['*'];

        // Act & Assert
        new ProjectionRequest($config, $request, $visibleColumns, $projectableColumns, $definedColumns);
    })->throws(Exception::class);

    it('should thrown an exception when defined columns are empty.', function (): void {
        // Prepare
        $config = [
            'key' => [
                'intersect' => 'columns',
                'except' => 'columns!',
            ],
        ];

        $request = [
            'columns' => 1,
        ];

        $visibleColumns = ['foo'];
        $projectableColumns = ['foo'];
        $definedColumns = [];

        // Act & Assert
        new ProjectionRequest($config, $request, $visibleColumns, $projectableColumns, $definedColumns);
    })->throws(Exception::class);

    it('should thrown an exception when all defined columns are not in visible columns.', function (): void {
        // Prepare
        $config = [
            'key' => [
                'intersect' => 'columns',
                'except' => 'columns!',
            ],
        ];

        $request = [
            'columns' => 1,
        ];

        $visibleColumns = ['foo', 'bar'];
        $projectableColumns = ['*'];
        $definedColumns = ['baz'];

        // Act & Assert
        new ProjectionRequest($config, $request, $visibleColumns, $projectableColumns, $definedColumns);
    })->throws(Exception::class);

    it('should thrown an exception when request value is not string.', function (): void {
        // Prepare
        $config = [
            'key' => [
                'intersect' => 'columns',
                'except' => 'columns!',
            ],
        ];

        $request = [
            'columns' => 1,
        ];

        // Act & Assert
        new ProjectionRequest($config, $request, ['foo'], ['foo'], ['*']);
    })->throws(Exception::class);

    it('should thrown an exception when requesting non-existing columns.', function (): void {
        // Prepare
        $config = [
            'key' => [
                'intersect' => 'columns',
                'except' => 'columns!',
            ],
        ];

        $request = [
            'columns' => 'baz',
        ];

        $visibleColumns = ['foo', 'bar'];
        $projectableColumns = ['*'];
        $definedColumns = ['*'];

        // Act & Assert
        new ProjectionRequest($config, $request, $visibleColumns, $projectableColumns, $definedColumns);
    })
        ->throws(Exception::class);

    test('should get request value of columns modifier', function (): void {
        // Prepare
        $config = [
            'key' => [
                'intersect' => 'columns',
                'except' => 'columns!',
            ],
        ];

        $request = [
            'columns' => 'baz',
        ];

        $visibleColumns = ['foo', 'bar', 'baz'];
        $projectableColumns = ['*'];
        $definedColumns = ['*'];

        // Act & Assert
        $request = new ProjectionRequest($config, $request, $visibleColumns, $projectableColumns, $definedColumns);
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

        $request = [
            'columns!' => 'baz',
        ];

        $visibleColumns = ['foo', 'bar', 'baz'];
        $projectableColumns = ['*'];
        $definedColumns = ['*'];

        // Act & Assert
        $request = new ProjectionRequest($config, $request, $visibleColumns, $projectableColumns, $definedColumns);
        expect($request())->toBe(['columns!' => ['foo', 'bar']]);
    });
});
