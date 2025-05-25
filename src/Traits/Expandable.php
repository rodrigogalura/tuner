<?php

namespace RGalura\ApiIgniter;

use RGalura\ApiIgniter\Services\ComponentResolver as Core;

trait Expandable
{
    private static function expand(array $expandable)
    {
        $clientExpand = $_GET['expand'] ?? [];

        if (empty($expandable) || empty($clientExpand)) {
            return [];
        }

        foreach ($clientExpand as $relation => $alias) {
            Core::bind("{$alias}_fields", fn () => static::fields($expandable[$relation]['projectable'], "{$alias}_fields", "{$alias}_fields!"));
            Core::bind("{$alias}_filter", fn () => static::filter($expandable[$relation]['filterable_fields'], "{$alias}_filter"));
            Core::bind("{$alias}_inFilter", fn () => static::inFilter($expandable[$relation]['filterable_fields'], "{$alias}_in"));
            Core::bind("{$alias}_betweenFilter", fn () => static::betweenFilter($expandable[$relation]['filterable_fields'], "{$alias}_between"));
            Core::bind("{$alias}_searchFilter", fn () => static::searchFilter($expandable[$relation]['searchable_fields'], "{$alias}_search"));
            Core::bind("{$alias}_sort", fn () => static::sort($expandable[$relation]['sortable_fields'], "{$alias}_sort"));

            foreach (array_keys(Core::$components) as $key) {
                try {
                    $$key = Core::resolve($key);
                } catch (\BadMethodCallException $e) {
                }
            }

            if ($fields = (${"{$alias}_fields"} ?? ['*'])) {
                $fk = $expandable[$relation]['fk'];

                /**
                 * Ensure the foreign key is added to the selected fields if:
                 * - A foreign key is defined,
                 * - Not all fields are being selected (`['*']` not used),
                 * - The foreign key is not already in the list of selected fields.
                 */
                if ($fk !== false && $fields !== ['*'] && ! in_array($fk, $fields)) {
                    array_push($fields, $fk);
                }
            }

            $expand[] = [
                'relation' => $relation,
                'table' => $expandable[$relation]['table'],
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
