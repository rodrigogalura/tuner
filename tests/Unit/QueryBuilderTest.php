<?php

use Illuminate\Database\Eloquent\Builder;
use Laradigs\Tweaker\CanTweak;

// it('example', function () {
// Prepare
// $builder = Mockery::mock(Builder::class)->makePartial();
// $builder->shouldReceive('get')->andReturn(['id', 'name']);
// $builder->columns = ['id', 'name'];

// $model = Mockery::mock(Model::class);

// $model->shouldReceive('columnListing')
//     ->andReturn(['id', 'name', 'turon']);

// $columnObject = new \stdClass;
// $columnObject->columns = ['id', 'name', 'turon'];

// $model->shouldReceive('getQuery')
//     ->andReturn($columnObject);

// $queryBuilder = new QueryBuilder($model, ['fields' => '*']);

// // Act
// $queryBuilder->setSelectFields(['id', 'name']);

// // Assert
// expect($queryBuilder->getSelectFields())->toBe(['id', 'name']);
// });

/* Usage:

// Model
class User extends Model {
    use \Laradigs\Tweaker\CanTweak;

    protected function getProjectableFields(): array
    {
        return ['*'];
    }
}


// Routes
Route::get('/users', function() {
    return User::send();
});

 */

beforeEach(function (): void {
    Mockery::globalHelpers();
});

it('should throw an exception if one of projectable fields is invalid', function (): void {
    $model = new class
    {
        use CanTweak;

        public function getProjectableFields()
        {
            return [1, 2, 3];
        }
    };

    $builderMock = mock(Builder::class);
    $model->scopeSend($builderMock);

    // $method = new \ReflectionMethod($model, 'getProjectableFields');

    // $model->scopeSend(new Builder);

    // expect(fn () => $model->send())->toThrow(\Exception::class);

    // expect($method->invoke($canTweak))->dump();
})->only();

it('should throw an exception if one of defined fields is invalid', function (): void {})->todo();

it('should not perform any action if the projectable field\'s value is falsy', function (): void {})->todo();
