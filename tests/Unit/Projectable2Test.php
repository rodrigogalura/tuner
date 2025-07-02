<?php

use RGalura\ApiIgniter\Exceptions\ExcludeFieldsException;
use RGalura\ApiIgniter\Exceptions\ImproperUsedProjectionException;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\HasDefaultValue;
use RGalura\ApiIgniter\Projectable2;

beforeEach(function () {
    $_GET = [];

    // Prepare
    $this->trait = new class
    {
        use HasDefaultValue;
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
            return ['a', 'b', 'c', 'd', 'e'];
        }

        public function getHidden()
        {
            return ['e'];
        }

        public function getTable()
        {
            return $this;
        }
    };

    $this->method = new \ReflectionMethod($this->trait, 'fieldsInput');
    $this->method->setAccessible(true);
});

it('should return null if the projectable fields are empty', function () {
    // Act and Assert
    expect($this->method->invoke($this->trait, []))->toBeNull();
});

it('should return null if neither "fields" nor "fields!" is used', function () {
    // Act and Assert
    expect($this->method->invoke($this->trait, ['a']))->toBeNull();
});

it('should throw and exception ImproperUsedProjectionException if the options "fields" and "fields!" are used at the same time', function () {
    $projectableFields = ['*'];

    $_GET['fields'] = 'foo';
    $_GET['fields!'] = 'bar';

    // Act and Assert
    expect(fn () => $this->method->invoke($this->trait, $projectableFields))->toThrow(ImproperUsedProjectionException::class);
});

it('should throw an exception InvalidFieldsException if the client fields are not exist', function (array $projectableFields, $clientFields, $expectedException) {
    $_GET['fields'] = $clientFields;

    // Act and Assert
    expect(fn () => $this->method->invoke($this->trait, $projectableFields))->toThrow($expectedException);
})
    ->with([
        ['projectableFields' => ['a', 'b'], 'clientFields' => 'c', 'expectedException' => InvalidFieldsException::class],
        ['projectableFields' => ['a', 'b'], 'clientFields' => 'b, c', 'expectedException' => InvalidFieldsException::class],
    ]);

it('should throw an exception ExcludeFieldsException if client fields! is *', function () {
    $projectableFields = ['*'];

    $_GET['fields!'] = '*';

    // Act and Assert
    expect(fn () => $this->method->invoke($this->trait, $projectableFields))->toThrow(ExcludeFieldsException::class);
});

test('a couple scenarios', function (array $projectableFields, $clientFields, $expectedReturn) {
    $_GET['fields'] = $clientFields;

    // Act and Assert
    expect($this->method->invoke($this->trait, $projectableFields))->toBe($expectedReturn);
})
    ->with([
        ['projectableFields' => ['a', 'b', 'c', 'd', 'e'], 'clientFields' => '*', 'expectedReturn' => ['a', 'b', 'c', 'd', 'e']],
        ['projectableFields' => ['a', 'b', 'c', 'd', 'e'], 'clientFields' => 'a, b, c', 'expectedReturn' => ['a', 'b', 'c']],
        ['projectableFields' => ['a', 'b', 'c', 'd', 'e'], 'clientFields' => 'c, d', 'expectedReturn' => [2 => 'c', 3 => 'd']],
        ['projectableFields' => ['e', 'd', 'c', 'b', 'a'], 'clientFields' => 'b, d', 'expectedReturn' => [1 => 'd', 3 => 'b']],
        ['projectableFields' => ['e', 'd', 'c', 'b', 'a'], 'clientFields' => 'e, c, a', 'expectedReturn' => ['e', 2 => 'c', 4 => 'a']],
    ]);

test('a couple scenarios for excluding fields', function (array $projectableFields, $clientFields, $expectedReturn) {
    $_GET['fields!'] = $clientFields;

    // Act and Assert
    expect($this->method->invoke($this->trait, $projectableFields))->toBe($expectedReturn);
})
    ->with([
        ['projectableFields' => ['a', 'b', 'c', 'd', 'e'], 'clientFields' => 'a, b, c', 'expectedReturn' => [3 => 'd', 4 => 'e']],
        ['projectableFields' => ['a', 'b', 'c', 'd', 'e'], 'clientFields' => 'c, d', 'expectedReturn' => ['a', 'b', 4 => 'e']],
        ['projectableFields' => ['e', 'd', 'c', 'b', 'a'], 'clientFields' => 'b, d', 'expectedReturn' => ['e', 2 => 'c', 4 => 'a']],
        ['projectableFields' => ['e', 'd', 'c', 'b', 'a'], 'clientFields' => 'e, c, a', 'expectedReturn' => [1 => 'd', 3 => 'b']],
    ]);
