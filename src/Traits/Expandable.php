<?php

namespace RGalura\ApiIgniter;

use Illuminate\Support\Arr;
use RGalura\ApiIgniter\Services\ComponentResolver as Core;

// use Schema;

trait Expandable
{
    use BetweenFilterable;
    use Filterable;
    use InFilterable;
    use Projectable;
    use Searchable;
    use Sortable;

    private static array $e_fields = [];

    private static function expand(array $expandable)
    {
        $fk = Arr::pull($expandable, 'fk');
        $clientExpand = $_GET['expand'] ?? [];

        if (empty($expandable) || empty($clientExpand)) {
            return [];
        }

        $expand = [];

        foreach ($clientExpand as $table => $alias) {
            // $expandable[$table]['projectable']['columnListing'] = Schema::getColumnListing($table);

            Core::bind("{$alias}_fields", fn () => static::fields($expandable[$table]['projectable'], "{$alias}_fields", "{$alias}_fields!"));
            Core::bind("{$alias}_filter", fn () => static::filter($expandable[$table]['filterable_fields'], "{$alias}_filter"));
            Core::bind("{$alias}_inFilter", fn () => static::inFilter($expandable[$table]['filterable_fields'], "{$alias}_in"));
            Core::bind("{$alias}_betweenFilter", fn () => static::betweenFilter($expandable[$table]['filterable_fields'], "{$alias}_between"));
            Core::bind("{$alias}_searchFilter", fn () => static::searchFilter($expandable[$table]['searchable_fields'], "{$alias}_search"));
            Core::bind("{$alias}_sort", fn () => static::sort($expandable[$table]['sortable_fields'], "{$alias}_sort"));

            foreach (array_keys(Core::$components) as $key) {
                try {
                    $$key = Core::resolve($key);
                } catch (\BadMethodCallException $e) {
                }
            }

            if ($fields = (${"{$alias}_fields"} ?? null)) {
                if ($fields !== ['*']) {
                    array_push($fields, $fk);
                }
            }

            $expand[] = [
                'table' => $table,
                'fields' => $fields,
                'filter' => ${"{$alias}_filter"} ?? [],
                'inFilter' => ${"{$alias}_inFilter"} ?? [],
                'betweenFilter' => ${"{$alias}_betweenFilter"} ?? [],
                'searchFilter' => ${"{$alias}_searchFilter"} ?? [],
                'sort' => ${"{$alias}_sort"} ?? [],
            ];
        }

        return $expand;
    }
}
