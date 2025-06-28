<?php

use RGalura\ApiIgniter\Searchable2;
use RGalura\ApiIgniter\HasDefaultValue;
use RGalura\ApiIgniter\Exceptions\ExcludeFieldsException;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Exceptions\ImproperUsedProjectionException;

beforeEach(function () {
    $_GET = [];

    // Prepare
    $this->trait = new class
    {
        use Searchable2;
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

    $this->method = new \ReflectionMethod($this->trait, 'searchFilter');
    $this->method->setAccessible(true);

});

test('a couple scenarios', function ($searchableFields, $clientFields, $expectedReturn) {
    $_GET['search'] = $clientFields;

    // Act and Assert
    expect($this->method->invoke($this->trait, $searchableFields))->toBe($expectedReturn);
})->with([
    ['searchableFields' => ['*'], 'clientFields' => ['a, b, c' => 'foo'], 'expectedReturn' => ['a, b, c' => '%foo%']],
    ['searchableFields' => ['*'], 'clientFields' => ['a, b, c' => '*foo*'], 'expectedReturn' => ['a, b, c' => '%foo%']],
    ['searchableFields' => ['*'], 'clientFields' => ['a, b, c' => '*foo'], 'expectedReturn' => ['a, b, c' => '%foo']],
    ['searchableFields' => ['*'], 'clientFields' => ['a, b, c' => 'foo*'], 'expectedReturn' => ['a, b, c' => 'foo%']],

    ['searchableFields' => ['a', 'b', 'c'], 'clientFields' => ['b, c' => 'foo'], 'expectedReturn' => ['b, c' => '%foo%']],
    ['searchableFields' => ['a', 'b', 'c'], 'clientFields' => ['b, c' => '*foo*'], 'expectedReturn' => ['b, c' => '%foo%']],
    ['searchableFields' => ['a', 'b', 'c'], 'clientFields' => ['b, c' => '*foo'], 'expectedReturn' => ['b, c' => '%foo']],
    ['searchableFields' => ['a', 'b', 'c'], 'clientFields' => ['b, c' => 'foo*'], 'expectedReturn' => ['b, c' => 'foo%']],

    // ['searchableFields' => ['b', 'c'], 'clientFields' => ['a, b, c' => 'foo'], 'expectedReturn' => ['b, c' => '%foo%']],
    // ['searchableFields' => ['b', 'c'], 'clientFields' => ['a, b, c' => '*foo*'], 'expectedReturn' => ['b, c' => '%foo%']],
    // ['searchableFields' => ['b', 'c'], 'clientFields' => ['a, b, c' => '*foo'], 'expectedReturn' => ['b, c' => '%foo']],
    // ['searchableFields' => ['b', 'c'], 'clientFields' => ['a, b, c' => 'foo*'], 'expectedReturn' => ['b, c' => 'foo%']],
]);
