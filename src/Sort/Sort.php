<?php

namespace Laradigs\Tweaker\Sort;

use Laradigs\Tweaker\DisabledException;
use Laradigs\Tweaker\Rules\SortRule;
use Laradigs\Tweaker\TruthTable;

use function RGalura\ApiIgniter\validate;

class Sort
{
    public const array ASCENDING_VALUES = [''];

    public const array DESCENDING_VALUES = ['d', 'des', 'desc', 'descending', '-'];

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

        $this->truthTable->extractIfAsterisk($this->sortableFields);
        $this->throwIfNotInVisibleFields($this->sortableFields, InvalidSortableException::class);
        $this->truthTable->intersectToAllItems($this->sortableFields);
    }

    protected function validate()
    {
        $this->prerequisites();

        validate($this->clientInput, [new SortRule($this->key, $this->sortableFields)]);
    }

    public function sort()
    {
        $this->validate();

        $sort = array_map(fn ($direction): string => in_array(strtolower($direction), static::DESCENDING_VALUES) ? 'DESC' : 'ASC', $this->clientInputValue);

        return array_filter($sort, fn ($field): bool => in_array($field, $this->sortableFields), ARRAY_FILTER_USE_KEY);
    }
}
