<?php

namespace RGalura\ApiIgniter;

use Illuminate\Support\Str;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Exceptions\MinimumKeywordException;

trait Searchable2
{
    private function searchedFields(array $searchableFields, int $minimum, string $clientKey = 'search')
    {
        if (empty($searchableFields) || ! isset($_GET[$clientKey])) {
            return null;
        }

        $fields = filter_explode(key($_GET[$clientKey]));
        if (! empty($diff = array_diff($fields, $searchableFields))) {
            throw new InvalidFieldsException(array_values($diff));
        }

        $keyword = current($_GET[$clientKey]);
        if (strlen(trim($keyword, '*')) < $minimum) {
            throw new MinimumKeywordException($minimum);
        }

        if (! str_starts_with($keyword, '*') && ! str_ends_with($keyword, '*')) {
            $keyword = "*{$keyword}*";
        }

        // convert asterisk to percentage of first and last position of keyword
        $keyword = Str::replaceMatches('/^\*|\*$/', '%', $keyword);

        return [implode(', ', array_intersect($searchableFields, $fields)) => $keyword];
    }
}
