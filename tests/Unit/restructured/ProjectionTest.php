<?php

use Laradigs\Tweaker\Projection\Projection;
use Laradigs\Tweaker\V31\Projection\IntersectProjection;

define('INTERSECT_KEY', 'fields');
define('EXCEPT_KEY', 'fields!');

dataset('not-string-value', [
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

test('test lang po', function (
    $visibleColumns,
    $projectableColumns,
    $definedColumns,
    $clientInput,
    $expectedResult
): void {
    $ip = new IntersectProjection(
        $visibleColumns,
        $projectableColumns,
        $definedColumns,
        $clientInput
    );

    try {
        $result = $ip->project();
    } catch (\Throwable $e) {
        $result = $e->getCode();
    }

    expect($result)->toBe($expectedResult);
})->with([
    [
        'visibleColumns' => ['id', 'name'],
        'projectableColumns' => ['id'],
        'definedColumns' => ['name'],
        'clientInput' => ['id', 'name'],
        'expectedResult' => 6,
    ],
])->skip();

test('Projection', function (
    $projection,
    $key,
    $projectableColumns,
    $definedColumns,
    $clientInput,
    $intersectResultNonStrict,
    $intersectResultStrict,
    $exceptResultNonStrict,
    $exceptResultStrict,
): void {
    // dd($projectableColumns,
    // $definedColumns,
    // $clientInput,
    // $intersectResultNonStrict,
    // $intersectResultStrict,
    // $exceptResultNonStrict,
    // $exceptResultStrict,);

    $visibleColumns = ['id', 'name'];
    $ip = new $projection(
        $visibleColumns,
        $projectableColumns,
        $definedColumns,
        [$key => $clientInput]
    );

    try {
        $result = $ip->project();
    } catch (\Throwable $e) {
        $result = $e->getCode();
    }

    expect($result)->toBe($intersectResultNonStrict);
})
    ->with([
        [IntersectProjection::class, INTERSECT_KEY],
        // [ExceptProjection::class, EXCEPT_KEY],
    ])
    ->with('projection');

// describe('Prerequisites', function (): void {
//     it('should throw DisabledException if the projectable fields are empty', function ($projection, $key, $projectableFields): void {
//         // Prepare
//         $projectionClass = new $projection(
//             $this->visibleFields,
//             $projectableFields,
//             definedFields: ['*'],
//             clientInput: [$key => $this->visibleFieldsString],
//         );

//         // Act & Expect Throws
//         $projectionClass->project();
//     })
//         ->with([
//             [IntersectProjection::class, INTERSECT_KEY],
//             [ExceptProjection::class, EXCEPT_KEY],
//         ])
//         ->with(['', null, [[]], false, 0, '0'])
//         ->throws(DisabledException::class);

//     it('should throw InvalidProjectableException if all projectable fields are not in visible fields', function ($projection, $key): void {
//         // Prepare
//         $notInVisibleFields = ['email'];
//         $projectionClass = new $projection(
//             $this->visibleFields,
//             projectableFields: $notInVisibleFields,
//             definedFields: ['*'],
//             clientInput: [$key => $this->visibleFieldsString],
//         );

//         // Act & Expect Throws
//         $projectionClass->project();
//     })
//         ->with([
//             [IntersectProjection::class, INTERSECT_KEY],
//             [ExceptProjection::class, EXCEPT_KEY],
//         ])
//         ->throws(InvalidProjectableException::class);

//     it('should throw DefinedFieldsAreEmptyException if the defined fields is empty', function ($projection, $key, $definedFields): void {
//         // Prepare
//         $projectionClass = new $projection(
//             $this->visibleFields,
//             projectableFields: ['id'],
//             definedFields: $definedFields,
//             clientInput: [$key => $this->visibleFieldsString],
//         );

//         // Act & Expect Throws
//         $projectionClass->project();
//     })
//         ->with([
//             [IntersectProjection::class, INTERSECT_KEY],
//             [ExceptProjection::class, EXCEPT_KEY],
//         ])
//         ->with(['', null, [[]], false, 0, '0'])
//         ->throws(DefinedFieldsAreEmptyException::class);

//     it('should throw InvalidDefinedFieldsException if all defined fields are not in visible fields', function ($projection, $key): void {
//         // Prepare
//         $notInVisibleFields = ['email'];
//         $projectionClass = new $projection(
//             $this->visibleFields,
//             projectableFields: ['*'],
//             definedFields: $notInVisibleFields,
//             clientInput: [$key => $this->visibleFieldsString],
//         );

//         // Act & Expect Throws
//         $projectionClass->project();
//     })
//         ->with([
//             [IntersectProjection::class, INTERSECT_KEY],
//             [ExceptProjection::class, EXCEPT_KEY],
//         ])
//         ->throws(InvalidDefinedFieldsException::class);

//     it('should throw InvalidDefinedFieldsException if all defined fields are not in projectable fields', function ($projection, $key): void {
//         // Prepare
//         $projectionClass = new $projection(
//             $this->visibleFields,
//             projectableFields: ['id', 'name'],
//             definedFields: ['email'],
//             clientInput: [$key => $this->visibleFieldsString],
//         );

//         // Act & Expect Throws
//         $projectionClass->project();
//     })
//         ->with([
//             [IntersectProjection::class, INTERSECT_KEY],
//             [ExceptProjection::class, EXCEPT_KEY],
//         ])
//         ->throws(InvalidDefinedFieldsException::class);
// });

// describe('Validations', function (): void {
//     it('should no available keys can use if both intersect and except projection are used', function (): void {
//         $projectableFields = $definedFields = ['*'];

//         // Prepare
//         $intersect = new IntersectProjection(
//             $this->visibleFields,
//             $projectableFields,
//             $definedFields,
//             [INTERSECT_KEY => $this->visibleFieldsString]
//         );

//         $except = new ExceptProjection(
//             $this->visibleFields,
//             $projectableFields,
//             $definedFields,
//             [EXCEPT_KEY => $this->visibleFieldsString]
//         );

//         // Act & Assert
//         expect(Projection::getKeyCanUse())->toBeEmpty();
//     })
//         ->throws(CannotUseMultipleProjectionException::class);

//     it('should throw ValidationException if the input is not string', function ($projection, $key, $input): void {
//         // // Prepare
//         $projectionClass = new $projection(
//             $this->visibleFields,
//             projectableFields: ['*'],
//             definedFields: ['*'],
//             clientInput: [$key => $input],
//         );

//         // Act & Expect Throws
//         $projectionClass->project();
//     })
//         ->with([
//             [IntersectProjection::class, INTERSECT_KEY],
//             [ExceptProjection::class, EXCEPT_KEY],
//         ])
//         ->with('not-string-value')
//         ->throws(ValidationException::class);

//     it('should throw ValidationException if the input exclude all available fields', function (): void {
//         // // Prepare
//         $projectionClass = new ExceptProjection(
//             $this->visibleFields,
//             projectableFields: ['*'],
//             definedFields: ['*'],
//             clientInput: [EXCEPT_KEY => $this->visibleFieldsString],
//         );

//         // Act & Expect Throws
//         $projectionClass->project();
//     })
//         ->throws(ValidationException::class);

//     it('should throw ValidationException if the input value is asterisk(*)', function (): void {
//         // // Prepare
//         $projectionClass = new ExceptProjection(
//             $this->visibleFields,
//             projectableFields: ['*'],
//             definedFields: ['*'],
//             clientInput: [EXCEPT_KEY => '*'],
//         );

//         // Act & Expect Throws
//         $projectionClass->project();
//     })
//         ->throws(ValidationException::class);
// });

// describe('Valid scenarios', function (): void {
//     it('should passed all valid scenarios for non strict intersect projection', function ($projectableFields, $definedFields, $clientInput, $intersectResultNonStrict): void {
//         // Prepare
//         $intersect = new IntersectProjection(
//             $this->visibleFields,
//             $projectableFields,
//             $definedFields,
//             [INTERSECT_KEY => $clientInput]
//         );

//         // Act & Assert
//         expect($intersect->project())->toBe($intersectResultNonStrict);
//     })
//         ->with('intersect-projection-non-strict-truth-table');

//     it('should passed all valid scenarios for strict intersect projection', function ($projectableFields, $definedFields, $clientInput, $intersectResultStrict): void {
//         // Prepare
//         $intersect = new IntersectProjection(
//             $this->visibleFields,
//             $projectableFields,
//             $definedFields,
//             [INTERSECT_KEY => $clientInput]
//         );

//         // Act & Assert
//         expect($intersect->project())->toBe($intersectResultStrict);
//     })
//         ->with('intersect-projection-strict-truth-table');
// });
