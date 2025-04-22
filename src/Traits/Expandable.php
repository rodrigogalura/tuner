<?php

namespace RGalura\ApiIgniter;

use Illuminate\Support\Arr;
use RGalura\ApiIgniter\Services\ComponentResolver as Core;

trait Expandable
{
    private static function expand(array $expandable)
    {
        $fk = Arr::pull($expandable, 'fk');
        $clientExpand = $_GET['expand'] ?? [];

        if (empty($expandable) || empty($clientExpand)) {
            return [];
        }

        foreach ($clientExpand as $relationship => $alias) {
            Core::bind("{$alias}_fields", fn () => static::fields($expandable[$relationship]['projectable'], "{$alias}_fields", "{$alias}_fields!"));
            Core::bind("{$alias}_filter", fn () => static::filter($expandable[$relationship]['filterable_fields'], "{$alias}_filter"));
            Core::bind("{$alias}_inFilter", fn () => static::inFilter($expandable[$relationship]['filterable_fields'], "{$alias}_in"));
            Core::bind("{$alias}_betweenFilter", fn () => static::betweenFilter($expandable[$relationship]['filterable_fields'], "{$alias}_between"));
            Core::bind("{$alias}_searchFilter", fn () => static::searchFilter($expandable[$relationship]['searchable_fields'], "{$alias}_search"));
            Core::bind("{$alias}_sort", fn () => static::sort($expandable[$relationship]['sortable_fields'], "{$alias}_sort"));

            foreach (array_keys(Core::$components) as $key) {
                try {
                    $$key = Core::resolve($key);
                } catch (\BadMethodCallException $e) {
                }
            }

            if ($fields = (${"{$alias}_fields"} ?? ['*'])) {
                if ($fields !== ['*']) {
                    array_push($fields, $fk);
                }
            }

            $expand[] = [
                'relationship' => $relationship,
                'fields' => $fields,
                'filter' => ${"{$alias}_filter"} ?? [],
                'inFilter' => ${"{$alias}_inFilter"} ?? [],
                'betweenFilter' => ${"{$alias}_betweenFilter"} ?? [],
                'searchFilter' => ${"{$alias}_searchFilter"} ?? [],
                'sort' => ${"{$alias}_sort"} ?? [],
            ];
        }

        return $expand ?? [];
    }
}
