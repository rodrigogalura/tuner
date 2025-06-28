<?php

use RGalura\ApiIgniter\Projectable2;
use RGalura\ApiIgniter\HasDefaultValue;
use RGalura\ApiIgniter\Exceptions\ExcludeFieldsException;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Exceptions\ImproperUsedProjectionException;

beforeEach(function () {
    $_GET = [];

    // Prepare
    $this->trait = new class
    {
        use Projectable2;
        use HasDefaultValue;

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

    $this->method = new \ReflectionMethod($this->trait, 'selectFields');
    $this->method->setAccessible(true);
});

test('a couple scenarios', function (array $projectableFields, $clientFields, $expectedReturn) {
    $_GET['fields'] = $clientFields;

    // Act and Assert
    expect($this->method->invoke($this->trait, $projectableFields))->toBe($expectedReturn);
})
    ->with([
        // ['projectableFields' => ['*'], 'clientFields' => '*', 'expectedReturn' => ['a', 'b', 'c', 'd']],
        ['projectableFields' => ['*'], 'clientFields' => '', 'expectedReturn' => ['a', 'b', 'c', 'd']],
        // ['projectableFields' => [''], 'clientFields' => '*', 'expectedReturn' => []],
        // ['projectableFields' => [''], 'clientFields' => '', 'expectedReturn' => []],
        // ['projectableFields' => ['a', 'b', 'c', 'd', 'e'], 'clientFields' => 'a, b, c', 'expectedReturn' => ['a', 'b', 'c']],
        // ['projectableFields' => ['a', 'b', 'c', 'd', 'e'], 'clientFields' => 'c, d', 'expectedReturn' => ['c', 'd']],
        // ['projectableFields' => ['e', 'd', 'c', 'b', 'a'], 'clientFields' => 'b, d', 'expectedReturn' => ['d', 'b']],
        // ['projectableFields' => ['e', 'd', 'c', 'b', 'a'], 'clientFields' => 'e, c, a', 'expectedReturn' => ['e', 'c', 'a']],
    ])->only();




it('should return empty array if the projectable fields is empty', function () {
    // Act and Assert
    expect($this->method->invoke($this->trait, []))->toBeEmpty();
});

it('should return projectable fields if client fields not provided', function () {
    $projectableFields = ['foo'];

    // Act and Assert
    expect($this->method->invoke($this->trait, $projectableFields))->toBe($projectableFields);
});

it('should return projectable fields if client fields value is *', function () {
    $projectableFields = ['foo'];
    $_GET['fields'] = '*';

    // Act and Assert
    expect($this->method->invoke($this->trait, $projectableFields))->toBe($projectableFields);
});

it('should return client fields if projectable fields is *', function () {
    $projectableFields = ['*'];
    $clientFields = $_GET['fields'] = 'id, name';

    // Act and Assert
    expect($this->method->invoke($this->trait, $projectableFields))->toBe(explode(', ', $clientFields));
});

test('a couple scenarios for excluding fields', function (array $projectableFields, $clientFields, $expectedReturn) {
    $_GET['fields!'] = $clientFields;

    // Act and Assert
    expect($this->method->invoke($this->trait, $projectableFields))->toBe($expectedReturn);
})
    ->with([
        ['projectableFields' => ['a', 'b', 'c', 'd', 'e'], 'clientFields' => 'a, b, c', 'expectedReturn' => ['d', 'e']],
        ['projectableFields' => ['a', 'b', 'c', 'd', 'e'], 'clientFields' => 'c, d', 'expectedReturn' => ['a', 'b', 'e']],
        ['projectableFields' => ['e', 'd', 'c', 'b', 'a'], 'clientFields' => 'b, d', 'expectedReturn' => ['e', 'c', 'a']],
        ['projectableFields' => ['e', 'd', 'c', 'b', 'a'], 'clientFields' => 'e, c, a', 'expectedReturn' => ['d', 'b']],
    ]);

test('a couple scenarios of InvalidFieldsException', function (array $projectableFields, $clientFields, $expectedException) {
    $_GET['fields'] = $clientFields;

    // Act and Assert
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

    // Act and Assert
    expect(fn () => $this->method->invoke($this->trait, $projectableFields))->toThrow(ImproperUsedProjectionException::class);
});

test('a scenario of ExcludeFieldsException', function () {
    $projectableFields = ['*'];

    $_GET['fields!'] = '*';

    // Act and Assert
    expect(fn () => $this->method->invoke($this->trait, $projectableFields))->toThrow(ExcludeFieldsException::class);
});
