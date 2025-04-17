<?php

namespace RGalura\ApiIgniter\Traits;

use RGalura\ApiIgniter\Services\QueryBuilder as Query;

use function RGalura\ApiIgniter\filter_explode;

trait Filterable
{
    private static function filter(array|string $filterableFields, string $client_key = 'filter')
    {
        if (is_string($filterableFields)) {
            $filterableFields = filter_explode($filterableFields);
        }

        if (is_string($clientParams = $_GET[$client_key] ?? [])) {
            $clientParams = [];
        }

        $clientFilter = array_filter($clientParams, fn ($key) => ! empty($key) && str_word_count($key) <= 2, ARRAY_FILTER_USE_KEY);

        if (empty($filterableFields) || empty($clientFilter)) {
            return [];
        }

        $query = [];
        foreach ($clientFilter as $key => $val) {
            array_push($query, array_merge(
                Query::boolField($key),
                Query::comparisonOperator($val)
            ));
        }

        return $filterableFields === ['*']
            ? $query
            : array_filter($query, fn ($expression) => in_array($expression[2], $filterableFields));
    }
}
