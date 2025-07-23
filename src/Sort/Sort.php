<?php

namespace Laradigs\Tweaker\Sort;

use Laradigs\Tweaker\Projection\NoActionWillPerformException;
use Laradigs\Tweaker\TruthTable;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;

use function RGalura\ApiIgniter\is_multi_array;

class Sort
{
    private const array DESCENDING_VALUES = ['d', 'des', 'desc', 'descending', '-'];

    protected TruthTable $truthTable;

    public function __construct(
        private array $visibleFields,
        protected array $sortableFields,
        private array $clientInput,
    ) {
        $this->truthTable = new TruthTable($visibleFields);

        $this->clientInput = current($clientInput);
    }

    private function prerequisites()
    {
        $sortIsNotLinearArray = is_multi_array($this->clientInput);
        throw_if(empty($this->clientInput) || $sortIsNotLinearArray, NoActionWillPerformException::class);
    }

    private function throwIfNotInVisibleFields(array $fields)
    {
        if (! empty($diff = $this->truthTable->diffFromAllItems($fields))) {
            throw new InvalidFieldsException($diff, 1);
        }
    }

    private function checkIfNotInVisibleFields(array $fields)
    {
        return ! empty($this->truthTable->diffFromAllItems($fields));
    }

    protected function validate()
    {
        $this->prerequisites();

        $this->truthTable->extractIfKeyIsAsterisk($this->clientInput);

        $fields = array_keys($this->clientInput);

        throw_if(
            empty($fields) ||
            $this->checkIfNotInVisibleFields($fields) ||
            empty($this->sortableFields),

            NoActionWillPerformException::class
        );

        $this->truthTable->extractIfAsterisk($this->sortableFields);
        $this->throwIfNotInVisibleFields($this->sortableFields);
    }

    public function sort()
    {
        $this->validate();

        $sort = array_map(fn ($direction): string => in_array(strtolower($direction), static::DESCENDING_VALUES) ? 'DESC' : 'ASC', $this->clientInput);

        return array_filter($sort, fn ($field): bool => in_array($field, $this->sortableFields), ARRAY_FILTER_USE_KEY);
    }
}
