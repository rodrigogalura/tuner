<?php

namespace RGalura\ApiIgniter;

use RGalura\ApiIgniter\Services\QueryBuilder as Query;

trait InFilterable
{
    /**
     * @return mixed[]
     */
    private static function inFilter(array|string $filterableFields, string $client_key = 'in'): array
    {
        if (is_string($filterableFields)) {
            $filterableFields = filter_explode($filterableFields);
        }

        if (is_string($clientParams = $_GET[$client_key] ?? [])) {
            $clientParams = [];
        }

        $clientIn = array_filter($clientParams ?? [], fn ($key): bool => ! empty($key) && in_array(str_word_count($key), [1, 2]), ARRAY_FILTER_USE_KEY);

        if (empty($filterableFields) || empty($clientIn)) {
            return [];
        }

        $inFilter = [];
        foreach ($clientIn as $key => $val) {
            array_push($inFilter, array_merge(
                Query::boolField($key),
                [filter_explode($val)]
            ));
        }

        return $filterableFields === ['*']
            ? $inFilter
            : array_filter($inFilter, fn ($expression): bool => in_array($expression[1], $filterableFields));
    }
}
