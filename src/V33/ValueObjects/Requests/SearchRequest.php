<?php

namespace RodrigoGalura\Tuner\V33\ValueObjects\Requests;

use Exception;
use RodrigoGalura\Tuner\V33\Tuner;
use RodrigoGalura\Tuner\V33\ValueObjects\Columns;
use RodrigoGalura\Tuner\V33\ValueObjects\SearchableColumns;

class SearchRequest extends SingleKeyColumnRequest
{
    public function __construct(
        private array $config,
        array $visibleColumns,
        array $searcableColumns,
        array $request
    ) {
        $validColumns = (new SearchableColumns($searcableColumns, $visibleColumns))();

        parent::__construct($config[Tuner::PARAM_KEY], $validColumns, $request);
    }

    private static function searchInterpreter($request)
    {
        foreach ($request as $searchKeyword) {
            // $filtered = array_filter(static::ORDERS, fn ($values, $key): bool => in_array($searchKeyword, $values), ARRAY_FILTER_USE_BOTH);
            // $request[$column] = key($filtered);

            // $keyword = current($_GET[$clientKey]);
            // if (strlen(trim($keyword, '*')) < $minimum) {
            //     throw new MinimumKeywordException($minimum);
            // }

            // if (! str_starts_with($keyword, '*') && ! str_ends_with($keyword, '*')) {
            //     $keyword = "*{$keyword}*";
            // }

            // // convert asterisk to percentage of first and last position of keyword
            // $keyword = Str::replaceMatches('/^\*|\*$/', '%', $keyword);

            // return [implode(', ', array_intersect($searchableFields, $fields)) => $keyword];
        }

        return $request;
    }

    protected function validate()
    {
        // Validate search
        $request = current($this->request); // unwrap
        throw_unless(is_array($request), new Exception('The '.$this->key.' must be array'));

        // Validate columns
        $columns = new Columns(array_keys($request), $this->validColumns);
        throw_if(empty($validColumns = $columns->intersect()->get()), new Exception('Invalid columns provided. It must be one of the following valid columns: '.implode(', ', $this->validColumns)));

        // Filter valid columns with insuffient keyword length
        $filteredRequest = array_filter($request, function (string $searchKeyword, string $column) use ($validColumns) {
            return in_array($column, $validColumns)
                && strlen($searchKeyword) < $this->config['minimum_length'];
        }, ARRAY_FILTER_USE_BOTH);

        // Validate values
        throw_unless(empty($filteredRequest), new Exception(sprintf('Keyword characters must be at least %d length for '.implode(', ', array_keys($filteredRequest)), $this->config['minimum_length'])));

        dd($filteredRequest);

        $interpreted = static::searchInterpreter($filteredRequest);
    }
}
