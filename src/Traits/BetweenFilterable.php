<?php

namespace RGalura\ApiIgniter;

use RGalura\ApiIgniter\Services\QueryBuilder as Query;

trait BetweenFilterable
{
    private static function betweenFilter(array|string $filterableFields, string $client_key = 'between')
    {
        if (is_string($filterableFields)) {
            $filterableFields = filter_explode($filterableFields);
        }

        if (is_string($clientParams = $_GET[$client_key] ?? [])) {
            $clientParams = [];
        }

        $clientBetween = array_filter($clientParams ?? [], function ($val, $key) {
            return ! empty($key) && str_word_count($key) <= 2
                    && preg_match('/^\w+(,|\s+|,\s+|\s+,\s+)\w+$/', $val);
        }, ARRAY_FILTER_USE_BOTH);

        if (empty($filterableFields) || empty($clientBetween)) {
            return [];
        }

        $betweenFilter = [];
        foreach ($clientBetween as $key => $val) {
            array_push($betweenFilter, array_merge(
                Query::boolField($key),
                [filter_explode($val)]
            ));
        }

        return $filterableFields === ['*']
            ? $betweenFilter
            : array_filter($betweenFilter, fn ($expression) => in_array($expression[2], $filterableFields));
    }
}
