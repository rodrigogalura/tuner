<?php

namespace RGalura\ApiIgniter;

trait Searchable
{
    /**
     * @return mixed[]
     */
    private static function searchFilter(array|string $searchableFields, string $client_key = 'search'): array
    {
        if (is_string($searchableFields)) {
            $searchableFields = filter_explode($searchableFields);
        }

        if (is_string($clientParams = $_GET[$client_key] ?? [])) {
            $clientParams = [];
        }

        $clientSearch = array_filter($clientParams ?? []);

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
