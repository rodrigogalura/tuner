<?php

namespace RGalura\ApiIgniter\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use function RGalura\ApiIgniter\filter_explode;

class QueryBuilder
{
    public static function boolField(string $boolField)
    {
        $boolFieldArr = filter_explode($boolField, ' ');

        switch (count($boolFieldArr)) {
            case 0:
                return [];

            case 1:
                $bool = 'AND';
                $field = $boolFieldArr[0];

                return [$bool, $field, false];

            default:
                $bool = strtoupper($boolFieldArr[0]);
                $field = $boolFieldArr[1];

                if ($not = str_ends_with($bool, '!')) {
                    $bool = rtrim($bool, '!');
                }

                return [$bool, $field, $not];
        }
    }

    public static function comparisonOperator(string $val)
    {
        if (empty($val)) {
            return ['=', ''];
        }

        return in_array($op = substr($val, 0, 2), ['>=', '<=', '<>'])
            ? [$op, trim(substr($val, 2))]
            : (in_array($op = $val[0], ['=', '>', '<'])
                ? [$op, trim(substr($val, 1))]
                : ['=', trim($val)]
            );
    }

    public static function filter(Builder|HasOne|HasMany|BelongsTo|BelongsToMany $q, $filter)
    {
        foreach ($filter as [$bool, $field, $not, $operator, $val]) {
            $q->where($field, $operator, $val, $bool.($not ? ' NOT' : ''));
        }
    }

    public static function inFilter(Builder|HasOne|HasMany|BelongsTo|BelongsToMany $q, $inFilter)
    {
        foreach ($inFilter as [$bool, $field, $not, $val]) {
            $q->whereIn($field, $val, $bool, $not);
        }
    }

    public static function betweenFilter(Builder|HasOne|HasMany|BelongsTo|BelongsToMany $q, $betweenFilter)
    {
        foreach ($betweenFilter as [$bool, $field, $not, $val]) {
            $q->whereBetween($field, $val, $bool, $not);
        }
    }

    public static function searchFilter(Builder|HasOne|HasMany|BelongsTo|BelongsToMany $q, $searchFilter)
    {
        $q->whereAny(filter_explode(key($searchFilter)), 'LIKE', current($searchFilter));
    }

    public static function sort(Builder|HasOne|HasMany|BelongsTo|BelongsToMany $q, array $sort, string $relationship='')
    {
        if (!empty($relationship)) {
            $relationship .= '.';
        }

        foreach ($sort as $field => $direction) {
            $q->orderBy("{$relationship}{$field}", $direction);
        }
    }
}
