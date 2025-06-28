<?php

namespace RGalura\ApiIgniter;

use Illuminate\Support\Str;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Exceptions\InvalidKeywordException;

trait Searchable2
{
    private function searchFilter(array $searchableFields, string $clientKey = 'search')
    {
        if (isset($_GET[$clientKey])) {
            $clientFields = filter_explode(key($_GET[$clientKey]));
            $keyword = current($_GET[$clientKey]);
        }

        if (empty($searchableFields) || empty($clientFields) || empty($keyword)) {
            return [];
        }

        if ($searchableFields === ['*']) {
            $searchableFields = $this->columnListing();
        }

        if (! empty($diff = array_diff($clientFields, $searchableFields))) {
            throw new InvalidFieldsException(array_values($diff));
        }

        if (strlen(trim($keyword, '*')) < $this->getMinimumKeywordCharForSearch()) {
            return [];
        }

        if (! str_starts_with($keyword, '*') && ! str_ends_with($keyword, '*')) {
            $keyword = "*{$keyword}*";
        }

        // convert asterisk to percentage of first and last position of keyword
        $keyword = Str::replaceMatches('/^\*|\*$/', '%', $keyword);

        return match (true) {
            $searchableFields === ['*'] => [key($_GET[$clientKey]) => $keyword],
            default => [implode(', ', array_intersect($searchableFields, $clientFields)) => $keyword],
        };
    }
}
