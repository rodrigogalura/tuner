<?php

namespace RodrigoGalura\Tuner\V33\ValueObjects\Requests;

use Exception;
use RodrigoGalura\Tuner\V33\ValueObjects\Columns;

class SortRequest extends SingleKeyColumnRequest
{
    private const ORDERS = [
        'asc' => ['a', 'asc', 'ascending'],
        'desc' => ['-', 'd', 'des', 'desc', 'descending'],
    ];

    private static function validValues()
    {
        return array_merge(static::ORDERS['asc'], static::ORDERS['desc']);
    }

    private static function orderInterpreter($request)
    {
        foreach ($request as $column => $order) {
            $filtered = array_filter(static::ORDERS, fn ($values, $key) => in_array($order, $values), ARRAY_FILTER_USE_BOTH);
            $request[$column] = key($filtered);
        }

        return $request;
    }

    protected function validate()
    {
        $request = current($this->request); // unwrap

        # Validate sort
        throw_unless(is_array($request), new Exception('The '.$this->key.' must be array'));

        $columns = new Columns(array_keys($request), $this->validColumns);

        # Validate columns
        throw_if(empty($validColumns = $columns()), new Exception('Invalid columns provided. It must be one of the following valid columns: '.implode(', ', $this->validColumns)));

        $validValues = static::validValues();

        $filteredRequest = array_filter($request, function ($order, $column) use ($validColumns, $validValues) {
            return in_array($column, $validColumns)
                && in_array($order, $validValues);
        }, ARRAY_FILTER_USE_BOTH);

        # Validate values
        throw_if(empty($filteredRequest), new Exception('The '.$this->key.' must be use any of these valid order: '.implode(', ', $validValues)));

        $this->request = static::orderInterpreter($filteredRequest);
    }
}
