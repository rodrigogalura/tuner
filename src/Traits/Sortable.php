<?php

namespace RGalura\ApiIgniter;

trait Sortable
{
    private static function sort(array|string $sortableFields, string $client_key = 'sort')
    {
        if (is_string($sortableFields)) {
            $sortableFields = filter_explode($sortableFields);
        }

        $clientSort = $_GET[$client_key] ?? [];

        if (empty($sortableFields) || empty($clientSort)) {
            return [];
        }

        $sort = array_map(fn ($direction) => in_array(strtolower($direction), ['d', 'des', 'desc', 'descending', '-']) ? 'DESC' : 'ASC', $clientSort);

        return $sortableFields === ['*']
            ? $sort
            : array_filter($sort, fn ($field) => in_array($field, $sortableFields), ARRAY_FILTER_USE_KEY);
    }
}
