<?php

namespace RGalura\ApiIgniter\Traits;

use function RGalura\ApiIgniter\filter_explode;

trait Searchable
{
    private static function searchFilter(array|string $searchableFields, string $client_key = 'search')
    {
        if (is_string($searchableFields)) {
            $searchableFields = filter_explode($searchableFields);
        }

        $clientSearch = array_filter($_GET[$client_key] ?? []);

        if (empty($searchableFields) || empty($clientSearch)) {
            return [];
        }

        $keyword = str_replace('*', '%', current($clientSearch));

        if (! str_starts_with($keyword, '%') && ! str_ends_with($keyword, '%')) {
            $keyword = "%{$keyword}%";
        }

        return match (true) {
            $searchableFields === ['*'] => [key($clientSearch) => $keyword],
            key($clientSearch) === ['*'] => [implode(', ', $searchableFields) => $keyword],
            default => [implode(', ', array_intersect($searchableFields, filter_explode(key($clientSearch)))) => $keyword],
        };
    }
}
