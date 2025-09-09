<?php

namespace RodrigoGalura\Tuner\V33\ValueObjects\Requests;

use Exception;
use RodrigoGalura\Tuner\V33\Tuner;
use RodrigoGalura\Tuner\V33\ValueObjects\Columns;
use RodrigoGalura\Tuner\V33\ValueObjects\SortableColumns;

class SortRequest extends Request
{
    private const ORDERS = [
        'asc' => ['a', 'asc', 'ascending'],
        'desc' => ['-', 'd', 'des', 'desc', 'descending'],
    ];

    public function __construct(
        array $config,
        array $request,
        private array $visibleColumns,
        private array $sortableColumns,
    ) {
        parent::__construct($config[Tuner::PARAM_KEY], $request);
    }

    private static function validOrderValues()
    {
        return array_merge(static::ORDERS['asc'], static::ORDERS['desc']);
    }

    private static function orderInterpreter($request)
    {
        foreach ($request as $column => $order) {
            $filtered = array_filter(static::ORDERS, fn ($values, $key): bool => in_array($order, $values), ARRAY_FILTER_USE_BOTH);
            $request[$column] = key($filtered);
        }

        return $request;
    }

    protected function validate()
    {
        $sortableColumns = (new SortableColumns($this->sortableColumns, $this->visibleColumns))();

        // Validate sort
        $sortRequest = current($this->request); // unwrap
        throw_unless(is_array($sortRequest), new Exception('The '.$this->key.' must be array'));

        // Validate columns
        $columns = new Columns(array_keys($sortRequest), $sortableColumns);
        throw_if(empty($requestedColumns = $columns->intersect()->get()), new Exception('Invalid columns provided. It must be one of the following sortable columns: '.implode(', ', $sortableColumns)));

        $filteredRequest = array_filter($sortRequest, fn ($column): bool => in_array($column, $requestedColumns), ARRAY_FILTER_USE_KEY);

        // Validate values
        $validOrderValues = static::validOrderValues();
        $filteredRequest = array_filter($filteredRequest, fn ($order): bool => in_array($order, $validOrderValues));

        throw_if(empty($filteredRequest), new Exception('The '.$this->key.' must be use any of these valid order: '.implode(', ', $validOrderValues)));

        $this->request = static::orderInterpreter($filteredRequest);
    }
}
