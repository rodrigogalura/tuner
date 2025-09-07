<?php

namespace RodrigoGalura\Tuner\V33\ValueObjects\Requests;

use Exception;
use RodrigoGalura\Tuner\V33\ValueObjects\Columns;

class SortRequest extends SingleKeyRequest
{
    private readonly array $sort;

    private const ORDERS = [
        'asc' => ['+', 'a', 'asc', 'ascending'],
        'desc' => ['-', 'd', 'des', 'desc', 'descending'],
    ];

    private static function validValues()
    {
        return array_merge(static::ORDERS['asc'], static::ORDERS['desc']);
    }

    private function setSort(array $request) {}

    protected function validate()
    {
        $request = current($this->request); // unwrap

        // Validate sort
        throw_unless(is_array($request), new Exception('The '.$this->key.' must be array'));

        $columns = new Columns(array_keys($request), $this->visibleColumns);

        // Validate columns
        throw_if(empty($validColumns = $columns()), new Exception('Invalid columns provided. It must be one of the following valid columns: '.implode(', ', $this->visibleColumns)));

        $validValues = static::validValues();

        $filteredRequest = array_filter($request, function ($order, $column) use ($validColumns, $validValues) {
            return in_array($column, $validColumns)
                && in_array($order, $validValues);
        }, ARRAY_FILTER_USE_BOTH);

        throw_if(empty($filteredRequest), new Exception('The '.$this->key.' must be use any of these valid order: '.implode(', ', $validValues)));

        $this->setSort($filteredRequest);
    }

    public function getSort(): ?string {}
}
