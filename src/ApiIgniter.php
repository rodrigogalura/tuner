<?php

namespace RGalura\ApiIgniter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use RGalura\ApiIgniter\Services\ComponentResolver as Core;
use RGalura\ApiIgniter\Services\QueryBuilder as Query;
use Schema;

trait ApiIgniter
{
    private static array $fields = ['*'];

    private static array $filter = [];

    private static array $inFilter = [];

    private static array $betweenFilter = [];

    private static array $searchFilter = [];

    private static array $sort = [];

    private static array $expand = [];

    private function preInit(&$projectable, &$expandable)
    {
        $projectable['columnListing'] = array_diff(
            $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable()),
            $this->getHidden()
        );

        foreach ($expandable as $table => $e) {
            if (is_array($e)) {
                $expandable[$table]['projectable']['columnListing'] = Schema::getColumnListing($table);
            }
        }

        $expandable['fk'] ??= $this->getForeignKey();
    }

    private static function init(
        array $projectable,
        array|string $filterableFields,
        array|string $searchableFields,
        array|string $sortableFields,
        array $expandable): void
    {
        Core::bind('fields', fn () => static::fields($projectable));
        Core::bind('filter', fn () => static::filter($filterableFields));
        Core::bind('inFilter', fn () => static::inFilter($filterableFields));
        Core::bind('betweenFilter', fn () => static::betweenFilter($filterableFields));
        Core::bind('searchFilter', fn () => static::searchFilter($searchableFields));
        Core::bind('sort', fn () => static::sort($sortableFields));
        Core::bind('expand', fn () => static::expand($expandable));

        foreach (array_keys(Core::$components) as $key) {
            try {
                self::$$key = Core::resolve($key);
            } catch (\BadMethodCallException $e) {
            }
        }
    }

    /**
     * Execute the query with filter
     */
    public function scopeSend(
        Builder $q,
        array $projectable = ['fields' => '*'],
        array|string $filterableFields = '*',
        array|string $searchableFields = '*',
        array|string $sortableFields = '*',
        array $expandable = [],
        bool $paginatable = false,
        bool $debuggable = false,
    ): mixed {
        $this->preInit($projectable, $expandable);
        self::init($projectable, $filterableFields, $searchableFields, $sortableFields, $expandable);

        try {
            $q->select(self::$fields);

            if (! empty(self::$filter)) {
                $q->where(fn ($q) => Query::filter($q, self::$filter));
            }

            if (! empty(self::$inFilter)) {
                $q->where(fn ($q) => Query::inFilter($q, self::$inFilter));
            }

            if (! empty(self::$betweenFilter)) {
                $q->where(fn ($q) => Query::betweenFilter($q, self::$betweenFilter));
            }

            if (! empty(self::$searchFilter)) {
                $q->where(fn ($q) => Query::searchFilter($q, self::$searchFilter));
            }

            Query::sort($q, self::$sort);

            foreach (self::$expand as $expand) {
                $q->with($expand['table'], function ($q) use ($expand) {
                    $q->select($expand['fields']);

                    if (! empty($expand['filter'])) {
                        Query::filter($q, $expand['filter']);
                    }

                    if (! empty($expand['inFilter'])) {
                        Query::inFilter($q, $expand['inFilter']);
                    }

                    if (! empty($expand['betweenFilter'])) {
                        Query::betweenFilter($q, $expand['betweenFilter']);
                    }

                    if (! empty($expand['searchFilter'])) {
                        Query::searchFilter($q, $expand['searchFilter']);
                    }

                    Query::sort($q, $expand['sort']);
                });
            }

            if ($limit = $_GET['limit'] ?? false) {
                $q->limit($limit);
            }

            if ($offset = $_GET['offset'] ?? false) {
                $q->offset($offset);
            }

            if ($debuggable && ($_GET['debug'] ?? false)) {
                return
                    print_r(['with' => self::$expand], true).PHP_EOL.
                    $q->toSql();
            }

            if ($paginatable && ($perPage = $_GET['perPage'] ?? false)) {
                return $q->paginate($perPage);
            }

            return $q->get();
        } catch (QueryException|\Exception $e) {
            return collect([]);
        }
    }
}
