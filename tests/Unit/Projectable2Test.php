<?php

use RGalura\ApiIgniter\Exceptions\ExcludeFieldsException;
use RGalura\ApiIgniter\Exceptions\ImproperUsedProjectionException;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Projectable2;

beforeEach(function () {
    $_GET = [];

    // Prepare
    $this->trait = new class
    {
        use Projectable2;

        public function getConnection()
        {
            return $this;
        }

        public function getSchemaBuilder()
        {
            return $this;
        }

        public function getColumnListing($table)
        {
            return ['id', 'name', 'email'];
        }

        public function getHidden()
        {
            return ['password'];
        }

        public function getTable()
        {
            return $this;
        }
    };

    // Act
    $this->method = new \ReflectionMethod($this->trait, 'projectedFields');
    $this->method->setAccessible(true);
});

it('should return empty array if the projectable fields is empty', function () {
    // Assert
    expect($this->method->invoke($this->trait, []))->toBeEmpty();
});

it('should return projectable fields if client fields not provided', function () {
    $projectableFields = ['foo'];

    // Assert
    expect($this->method->invoke($this->trait, $projectableFields))->toBe($projectableFields);
});

it('should return projectable fields if client fields value is *', function () {
    $projectableFields = ['foo'];
    $_GET['fields'] = '*';

    // Assert
    expect($this->method->invoke($this->trait, $projectableFields))->toBe($projectableFields);
});

it('should return client fields if projectable fields is *', function () {
    $projectableFields = ['*'];
    $clientFields = $_GET['fields'] = 'id, name';

    // Assert
    expect($this->method->invoke($this->trait, $projectableFields))->toBe(explode(', ', $clientFields));
});

test('a couple scenarios', function (array $projectableFields, $clientFields, $expectedResult) {
    $_GET['fields'] = $clientFields;

    // Assert
    expect($this->method->invoke($this->trait, $projectableFields))->toBe($expectedResult);
})
    ->with([
        ['projectableFields' => ['a', 'b', 'c', 'd', 'e'], 'clientFields' => 'a, b, c', 'expectedResult' => ['a', 'b', 'c']],
        ['projectableFields' => ['a', 'b', 'c', 'd', 'e'], 'clientFields' => 'c, d', 'expectedResult' => ['c', 'd']],
        ['projectableFields' => ['e', 'd', 'c', 'b', 'a'], 'clientFields' => 'b, d', 'expectedResult' => ['d', 'b']],
        ['projectableFields' => ['e', 'd', 'c', 'b', 'a'], 'clientFields' => 'e, c, a', 'expectedResult' => ['e', 'c', 'a']],
    ]);

test('a couple scenarios for excluding fields', function (array $projectableFields, $clientFields, $expectedResult) {
    $_GET['fields!'] = $clientFields;

    // Assert
    expect($this->method->invoke($this->trait, $projectableFields))->toBe($expectedResult);
})
    ->with([
        ['projectableFields' => ['a', 'b', 'c', 'd', 'e'], 'clientFields' => 'a, b, c', 'expectedResult' => ['d', 'e']],
        ['projectableFields' => ['a', 'b', 'c', 'd', 'e'], 'clientFields' => 'c, d', 'expectedResult' => ['a', 'b', 'e']],
        ['projectableFields' => ['e', 'd', 'c', 'b', 'a'], 'clientFields' => 'b, d', 'expectedResult' => ['e', 'c', 'a']],
        ['projectableFields' => ['e', 'd', 'c', 'b', 'a'], 'clientFields' => 'e, c, a', 'expectedResult' => ['d', 'b']],
    ]);

test('a couple scenarios of InvalidFieldsException', function (array $projectableFields, $clientFields, $expectedException) {
    $_GET['fields'] = $clientFields;

    // Assert
    expect(fn () => $this->method->invoke($this->trait, $projectableFields))->toThrow($expectedException);
})
    ->with([
        ['projectableFields' => ['id', 'name'], 'clientFields' => 'email', 'expectedException' => InvalidFieldsException::class],
        ['projectableFields' => ['name', 'email'], 'clientFields' => 'email, password', 'expectedException' => InvalidFieldsException::class],
    ]);

test('a scenario of ImproperUsedProjectionException', function () {
    $projectableFields = ['*'];

    $_GET['fields'] = 'foo';
    $_GET['fields!'] = 'bar';

    // Assert
    expect(fn () => $this->method->invoke($this->trait, $projectableFields))->toThrow(ImproperUsedProjectionException::class);
});

test('a scenario of ExcludeFieldsException', function () {
    $projectableFields = ['*'];

    $_GET['fields!'] = '*';

    // Assert
    expect(fn () => $this->method->invoke($this->trait, $projectableFields))->toThrow(ExcludeFieldsException::class);
});
