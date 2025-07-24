<?php

namespace Laradigs\Tweaker\Sort;

use Laradigs\Tweaker\TruthTable;
use Laradigs\Tweaker\DisabledException;
use Laradigs\Tweaker\Rules\LinearArray;
use function RGalura\ApiIgniter\validate;
use Laradigs\Tweaker\Rules\ValidArrayKeys;
use function RGalura\ApiIgniter\is_multi_array;
use Laradigs\Tweaker\Sort\InvalidSortableException;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;

class Sort
{
    private const array DESCENDING_VALUES = ['d', 'des', 'desc', 'descending', '-'];

    protected TruthTable $truthTable;

    protected readonly string $key;
    protected mixed $clientInputValue;

    public function __construct(
        private array $visibleFields,
        protected mixed $sortableFields,
        private array $clientInput,
    ) {
        $this->truthTable = new TruthTable($visibleFields);

        $this->key = key($clientInput);
        $this->clientInputValue = current($clientInput);
    }

    private function throwIfNotInVisibleFields(array $fields, $exception)
    {
        throw_if($diff = $this->truthTable->diffFromAllItems($fields), new $exception($diff));
    }

    private function prerequisites()
    {
        throw_if(empty($this->sortableFields), DisabledException::class);
        $this->throwIfNotInVisibleFields($this->sortableFields, InvalidSortableException::class);
    }

    private function checkIfNotInVisibleFields(array $fields)
    {
        return ! empty($this->truthTable->diffFromAllItems($fields));
    }

    protected function validate()
    {
        $this->prerequisites();

        validate($this->clientInput, [
            new LinearArray,
            new ValidArrayKeys($this->visibleFields),
        ]);

        $this->truthTable->extractIfKeyIsAsterisk($this->clientInputValue);

        $validValues = implode(',', static::DESCENDING_VALUES);

        validate($this->clientInputValue,
            ['in:' . $validValues],
            "The {$this->key} with :attribute value is not valid. It must be one of the valid values: {$validValues}"
        );
    }

    public function sort()
    {
        $this->validate();

        $sort = array_map(fn ($direction): string => in_array(strtolower($direction), static::DESCENDING_VALUES) ? 'DESC' : 'ASC', $this->clientInputValue);

        return array_filter($sort, fn ($field): bool => in_array($field, $this->sortableFields), ARRAY_FILTER_USE_KEY);
    }
}
