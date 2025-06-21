<?php

namespace RGalura\ApiIgniter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use RGalura\ApiIgniter\Services\ComponentResolver as Core;
use RGalura\ApiIgniter\Services\QueryBuilder as Query;
use Schema;

trait ApiIgniter
{
    protected array $projectableFields = ['*'];

    protected array $projectedFields = ['*'];

    private static array $fields = ['*'];

    private static array $filter = [];

    private static array $inFilter = [];

    private static array $betweenFilter = [];

    private static array $searchFilter = [];

    private static array $sort = [];

    private static array $expand = [];

    private function preInit(&$expandable)
    {
        // $projectable['columnListing'] = array_diff(
        //     $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable()),
        //     $this->getHidden()
        // );

        // foreach ($expandable as $relation => $e) {
        //     $e['table'] ??= ($expandable[$relation]['table'] = Str::of($relation)->plural()->value);

        //     $expandable[$relation]['projectable']['columnListing'] = Schema::getColumnListing($e['table']);
        //     $expandable[$relation]['fk'] ??= $this->getForeignKey();
        // }
    }

    private function init(
        array|string $filterableFields,
        array|string $searchableFields,
        array|string $sortableFields,
        array $expandable): void
    {
        Core::bind('projectedFields', fn () => $this->projectedFields($this->projectableFields));

        foreach (array_keys(Core::$components) as $key) {
            try {
                $this->{$key} = Core::resolve($key);
            } catch (\BadMethodCallException $e) {
                // noop
            } catch (\Throwable $e) {
                $STRICT = 1;
                if ($STRICT) {
                    throw $e;
                }

                $this->{$key} = [];
            }
        }

        // Core::bind('fields', fn () => static::fields($projectable));
        // Core::bind('filter', fn () => static::filter($filterableFields));
        // Core::bind('inFilter', fn () => static::inFilter($filterableFields));
        // Core::bind('betweenFilter', fn () => static::betweenFilter($filterableFields));
        // Core::bind('searchFilter', fn () => static::searchFilter($searchableFields));
        // Core::bind('sort', fn () => static::sort($sortableFields));
        // Core::bind('expand', fn () => static::expand($expandable));

        // foreach (array_keys(Core::$components) as $key) {
        //     try {
        //         self::$$key = Core::resolve($key);
        //     } catch (\BadMethodCallException $e) {
        //     }
        // }
    }

    /**
     * Execute the query with filter
     */
    public function scopeSend(
        Builder $builder,
        // array|string $projectable = '*',
        array|string $filterableFields = '*',
        array|string $searchableFields = '*',
        array|string $sortableFields = '*',
        array $expandable = [],
        bool $paginatable = false,
        bool $debuggable = false,
    ): mixed {
        $this->preInit($expandable);
        $this->init($filterableFields, $searchableFields, $sortableFields, $expandable);

        try {
            $builder->select($this->projectedFields);

            // $builder->select(self::$fields);

            if (! empty(self::$filter)) {
                $builder->where(fn ($builder) => Query::filter($builder, self::$filter));
            }

            if (! empty(self::$inFilter)) {
                $builder->where(fn ($builder) => Query::inFilter($builder, self::$inFilter));
            }

            if (! empty(self::$betweenFilter)) {
                $builder->where(fn ($builder) => Query::betweenFilter($builder, self::$betweenFilter));
            }

            if (! empty(self::$searchFilter)) {
                $builder->where(fn ($builder) => Query::searchFilter($builder, self::$searchFilter));
            }

            Query::sort($builder, self::$sort);

            foreach (self::$expand as $expand) {
                $builder->with($expand['relation'], function ($builder) use ($expand) {
                    $builder->select(array_map(fn ($field) => $expand['table'].'.'.$field, $expand['fields']));

                    if (! empty($expand['filter'])) {
                        Query::filter($builder, $expand['filter']);
                    }

                    if (! empty($expand['inFilter'])) {
                        Query::inFilter($builder, $expand['inFilter']);
                    }

                    if (! empty($expand['betweenFilter'])) {
                        Query::betweenFilter($builder, $expand['betweenFilter']);
                    }

                    if (! empty($expand['searchFilter'])) {
                        Query::searchFilter($builder, $expand['searchFilter']);
                    }

                    Query::sort($builder, $expand['sort'], $expand['table']);
                });
            }

            if ($limit = $_GET['limit'] ?? false) {
                $builder->limit($limit);
            }

            if ($offset = $_GET['offset'] ?? false) {
                $builder->offset($offset);
            }

            if ($debuggable && ($_GET['debug'] ?? false)) {
                return
                    print_r(['with' => self::$expand], true).PHP_EOL.
                    $builder->toSql();
            }

            if ($paginatable && ($perPage = $_GET['per-page'] ?? false)) {
                return $builder->paginate($perPage);
            }

            return $builder->get();
        } catch (QueryException|\Exception $e) {
            return collect($debuggable ? [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ] : []);
        }
    }
}
