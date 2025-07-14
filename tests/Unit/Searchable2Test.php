<?php

use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Exceptions\MinimumKeywordException;
use RGalura\ApiIgniter\HasDefaultValue;
use RGalura\ApiIgniter\Searchable2;

beforeEach(function (): void {
    $_GET = [];

    // Prepare
    $this->trait = new class
    {
        use HasDefaultValue;
        use Searchable2;

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

    $this->method = new \ReflectionMethod($this->trait, 'searchInput');
    $this->method->setAccessible(true);

    $this->minimum = 2;
});

it('should return null if the searchable fields are empty', function (): void {
    // Act and Assert
    expect($this->method->invoke($this->trait, [], $this->minimum))->toBeNull();
});

it('should return null if the search option is not used', function (): void {
    // Act and Assert
    expect($this->method->invoke($this->trait, ['a'], $this->minimum))->toBeNull();
});

test('should throw an exception InvalidFieldsException if the client fields are not exist', function (array $searchableFields, $clientFields, $expectedException): void {
    $_GET['search'] = $clientFields;

    // Act and Assert
    expect(fn () => $this->method->invoke($this->trait, $searchableFields, $this->minimum))->toThrow($expectedException);
})
    ->with([
        ['searchableFields' => ['a', 'b'], 'clientFields' => ['c' => 'search me'], 'expectedException' => InvalidFieldsException::class],
        ['searchableFields' => ['a', 'b'], 'clientFields' => ['b, c' => 'search me'], 'expectedException' => InvalidFieldsException::class],
    ]);

it('should throw an exception MinimumKeywordException if the search keyword length not hit the minimum', function (): void {
    $_GET['search'] = ['a' => str_repeat('b', $this->minimum - 1)];

    // Act and Assert
    expect(fn () => $this->method->invoke($this->trait, ['a'], $this->minimum))->toThrow(MinimumKeywordException::class);
});

test('a couple scenarios', function ($searchableFields, $clientFields, $expectedReturn): void {
    $_GET['search'] = $clientFields;

    // Act and Assert
    expect($this->method->invoke($this->trait, $searchableFields, $this->minimum))->toBe($expectedReturn);
})->with([
    ['searchableFields' => ['a', 'b', 'c'], 'clientFields' => ['b, c' => 'foo'], 'expectedReturn' => ['b, c' => '%foo%']],
    ['searchableFields' => ['a', 'b', 'c'], 'clientFields' => ['b, c' => '*foo*'], 'expectedReturn' => ['b, c' => '%foo%']],
    ['searchableFields' => ['a', 'b', 'c'], 'clientFields' => ['b, c' => '*foo'], 'expectedReturn' => ['b, c' => '%foo']],
    ['searchableFields' => ['a', 'b', 'c'], 'clientFields' => ['b, c' => 'foo*'], 'expectedReturn' => ['b, c' => 'foo%']],
])->only();
