<?php

namespace RGalura\ApiIgniter;

use RGalura\ApiIgniter\Services\QueryBuilder as Query;

trait Filterable
{
    /**
     * @return mixed[]
     */
    private static function filter(array|string $filterableFields, string $client_key = 'filter'): array
    {
        if (is_string($filterableFields)) {
            $filterableFields = filter_explode($filterableFields);
        }

        if (is_string($clientParams = $_GET[$client_key] ?? [])) {
            $clientParams = [];
        }

        $clientFilter = array_filter($clientParams, fn ($key): bool => ! empty($key) && str_word_count($key) <= 2, ARRAY_FILTER_USE_KEY);

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
            : array_filter($query, fn ($expression): bool => in_array($expression[2], $filterableFields));
    }
}
