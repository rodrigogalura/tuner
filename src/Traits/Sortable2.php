<?php

namespace RGalura\ApiIgniter;

trait Sortable2
{
    private static function sortInput(array $sortableFields, string $clientKey = 'sort')
    {
        if (empty($sortableFields) || ! isset($_GET[$clientKey])) {
            return null;
        }

        $sort = array_map(fn ($direction): string => in_array(strtolower($direction), ['d', 'des', 'desc', 'descending', '-']) ? 'DESC' : 'ASC', $_GET[$clientKey]);

        return array_filter($sort, fn ($field): bool => in_array($field, $sortableFields), ARRAY_FILTER_USE_KEY);
    }
}
