<?php

use Laradigs\Tweaker\Projection\ExceptProjection;
use Laradigs\Tweaker\Projection\IntersectProjection;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;
use Laradigs\Tweaker\Projection\Projection;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Exceptions\NoDefinedFieldException;

dataset('not-string-value', [
    [[]],
    [[1]], [['1']],
    [[10]], [['20']],
    [[100]], [['300']],
    [['a']], [['A']],
    [['@']], [['!']],
]);

beforeEach(function (): void {
    $this->visibleFields = ['id', 'name'];
    $this->visibleFieldsString = implode(', ', $this->visibleFields);
});

afterEach(function (): void {
    Projection::clearKeys();
});

describe('Not meet the requirements', function (): void {
    it('should no available keys can use if both intersect and except projection are used', function (): void {
        $projectableFields = $definedFields = ['*'];

        // Prepare
        $intersect = new IntersectProjection(
            $this->visibleFields,
            $projectableFields,
            $definedFields,
            ['fields' => 'foo']
        );

        $except = new ExceptProjection(
            $this->visibleFields,
            $projectableFields,
            $definedFields,
            ['fields!' => 'bar']
        );

        // Act & Assert
        expect(Projection::getKeyCanUse())->toBeEmpty();
    });
});

describe('Prerequisites', function (): void {
    it('should throw NoActionWillPerformException if the "fields" value is not string', function ($input): void {
        // Prepare
        $projection = new IntersectProjection(
            $this->visibleFields,
            projectableFields: ['*'],
            definedFields: ['*'],
            clientInput: ['fields' => $input],
        );

        // Act & Assert
        expect(fn () => $projection->project())->toThrow(NoActionWillPerformException::class);
    })->with('not-string-value');

    it('should throw NoActionWillPerformException if the "fields!" value is not string', function ($input): void {
        // Prepare
        $projection = new ExceptProjection(
            $this->visibleFields,
            projectableFields: ['*'],
            definedFields: ['*'],
            clientInput: ['fields!' => $input],
        );

        // Act & Assert
        expect(fn () => $projection->project())->toThrow(NoActionWillPerformException::class);
    })->with('not-string-value');
});

describe('Validations', function (): void {
    it('should throw NoActionWillPerformException if the "fields" value is empty', function (): void {
        // Prepare
        $projection = new IntersectProjection(
            $this->visibleFields,
            projectableFields: ['*'],
            definedFields: ['*'],
            clientInput: ['fields' => ''],
        );

        // Act & Assert
        expect(fn () => $projection->project())->toThrow(NoActionWillPerformException::class);
    });

    it('should throw NoActionWillPerformException if the "fields!" value is *', function (): void {
        // Prepare
        $projection = new ExceptProjection(
            $this->visibleFields,
            projectableFields: ['*'],
            definedFields: ['*'],
            clientInput: ['fields' => '*'],
        );

        // Act & Assert
        expect(fn () => $projection->project())->toThrow(NoActionWillPerformException::class);
    });

    it('should throw NoActionWillPerformExceptionn if the projectable field\'s value is empty', function (): void {
        // Prepare
        $DEFINE_FIELDS = ['*'];
        $projection = new IntersectProjection(
            $this->visibleFields,
            projectableFields: [],
            definedFields: $DEFINE_FIELDS,
            clientInput: ['fields' => ''],
        );

        // Act & Assert
        expect(fn () => $projection->project())->toThrow(NoActionWillPerformException::class);
    });

    it('should throw NoActionWillPerformExceptionn if the projectable fields and defined fields are not intersect', function (): void {
        // Prepare
        $DEFINE_FIELDS = [$this->visibleFields[1]];
        $projection = new IntersectProjection(
            $this->visibleFields,
            projectableFields: [$this->visibleFields[0]],
            definedFields: $DEFINE_FIELDS,
            clientInput: ['fields' => 'foo'],
        );

        // Act & Assert
        expect(fn () => $projection->project())->toThrow(NoActionWillPerformException::class);
    });

    describe('Throw an exception', function (): void {
        it('should throw an exception if one of projectable fields is invalid', function (): void {
            // Prepare
            $notInVisibleFields = ['email'];
            $projection = new IntersectProjection(
                $this->visibleFields,
                projectableFields: $notInVisibleFields,
                definedFields: [],
                clientInput: ['fields' => $this->visibleFieldsString],
            );

            // Act & Assert
            expect(fn () => $projection->project())->toThrow(InvalidFieldsException::class);
        });

        it('should throw an exception if the defined fields is empty', function (): void {
            // Prepare
            $projection = new IntersectProjection(
                $this->visibleFields,
                projectableFields: ['id'],
                definedFields: [],
                clientInput: ['fields' => $this->visibleFieldsString],
            );

            // Act & Assert
            expect(fn () => $projection->project())->toThrow(NoDefinedFieldException::class);
        });

        it('should throw an exception if one of defined fields is invalid', function (): void {
            // Prepare
            $notInVisibleFields = ['email'];
            $projection = new IntersectProjection(
                $this->visibleFields,
                projectableFields: ['id'],
                definedFields: $notInVisibleFields,
                clientInput: ['fields' => $this->visibleFieldsString],
            );

            // Act & Assert
            expect(fn () => $projection->project())->toThrow(InvalidFieldsException::class);
        });
    });
});

describe('Valid scenarios', function (): void {
    it('should passed all valid scenarios for client input "fields"', function ($projectableFields, $definedFields, $clientInput, $intersectResult): void {
        // Prepare
        $intersect = new IntersectProjection(
            $this->visibleFields,
            $projectableFields,
            $definedFields,
            ['fields' => $clientInput]
        );

        // Act & Assert
        expect($intersect->project())->toBe($intersectResult);
    })
        ->with('intersect-projection-truth-table');

    it('should passed all valid scenarios for client input "fields!"', function ($projectableFields, $definedFields, $clientInput, $exceptResult): void {
        // Prepare
        $except = new ExceptProjection(
            $this->visibleFields,
            $projectableFields,
            $definedFields,
            ['fields!' => $clientInput]
        );

        // Act & Assert
        expect($except->project())->toBe($exceptResult);
    })
        ->with('except-projection-truth-table');
});
