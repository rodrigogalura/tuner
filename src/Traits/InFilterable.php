<?php

namespace RGalura\ApiIgniter\Traits;

use RGalura\ApiIgniter\Services\QueryBuilder as Query;

use function RGalura\ApiIgniter\filter_explode;

trait InFilterable
{
    private static function inFilter(array|string $filterableFields, string $client_key = 'in')
    {
        if (is_string($filterableFields)) {
            $filterableFields = filter_explode($filterableFields);
        }

        $clientIn = array_filter($_GET[$client_key] ?? [], fn ($key) => ! empty($key) && in_array(str_word_count($key), [1, 2]), ARRAY_FILTER_USE_KEY);

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
            : array_filter($inFilter, fn ($expression) => in_array($expression[1], $filterableFields));
    }
}
