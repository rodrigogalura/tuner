<?php

namespace RGalura\ApiIgniter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use RGalura\ApiIgniter\Exceptions\InvalidProjectableFieldsException;
use RGalura\ApiIgniter\Services\ComponentResolver as Core;
use RGalura\ApiIgniter\Services\QueryBuilder as Query;
use Schema;

trait ApiIgniter
{
    use HasDefaultValue;

    protected ?array $projectedFields = null;

    protected array $searchFilter = [];

    private static array $fields = ['*'];

    private static array $filter = [];

    private static array $inFilter = [];

    private static array $betweenFilter = [];

    // private static array $searchFilter = [];

    private static array $sort = [];

    private static array $expand = [];

    private readonly array $givenFields;

    private array $projectableFields;

    private function preInit(&$expandable)
    {
        // foreach ($expandable as $relation => $e) {
        //     $e['table'] ??= ($expandable[$relation]['table'] = Str::of($relation)->plural()->value);

        //     $expandable[$relation]['projectable']['columnListing'] = Schema::getColumnListing($e['table']);
        //     $expandable[$relation]['fk'] ??= $this->getForeignKey();
        // }
    }

    private function init(
        Builder $builder,
        array|string $filterableFields,
        array|string $searchableFields,
        array|string $sortableFields,
        array $expandable): void
    {
        // Represent as "*"
        $this->givenFields = array_diff(
            $builder->getQuery()->columns ?? $this->columnListing(),
            $this->getHidden()
        );

        Core::bind('projectedFields', function () {
            $this->projectableFields = $this->givenFields;

            if (($projectableFields = $this->getProjectableFields()) !== ['*']) {
                // Config Validation
                (function () use ($projectableFields) {
                    if (! empty($diff = array_diff($projectableFields, $this->givenFields))) {
                        throw new InvalidProjectableFieldsException($diff);
                    }
                })();

                $this->projectableFields = array_intersect($this->givenFields, $projectableFields);
            }

            return $this->projectedFields($this->projectableFields);
        });
        // Core::bind('projectedFields', fn () => $this->projectedFields($this->projectableFields));
        // Core::bind('searchFilter', fn () => $this->searchFilter($this->getSearchableFields()));

        foreach (array_keys(Core::$components) as $key) {
            try {
                $this->{$key} = Core::resolve($key);
            } catch (\BadMethodCallException $e) {
                // noop
            } catch (\Throwable $e) {
                $STRICT_CODE = 1;
                if ($e->getCode() === $STRICT_CODE || $this->canInspect()) {
                    throw $e;
                }
            }
        }

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
        $this->init($builder, $filterableFields, $searchableFields, $sortableFields, $expandable);

        try {
            if (! is_null($this->projectedFields)) {
                $combinedFieldsResult = array_diff($this->givenFields, $this->projectableFields) + $this->projectedFields;

                ksort($combinedFieldsResult, SORT_NUMERIC);

                $builder->select($combinedFieldsResult);
            }

            if (! empty(self::$filter)) {
                $builder->where(fn ($builderInner) => Query::filter($builderInner, self::$filter));
            }

            if (! empty(self::$inFilter)) {
                $builder->where(fn ($builderInner) => Query::inFilter($builderInner, self::$inFilter));
            }

            if (! empty(self::$betweenFilter)) {
                $builder->where(fn ($builderInner) => Query::betweenFilter($builderInner, self::$betweenFilter));
            }

            // if (! empty(self::$searchFilter)) {
            //     $builder->where(fn ($builderInner) => Query::searchFilter($builderInner, self::$searchFilter));
            // }

            if (! empty($this->searchFilter)) {
                $builder->where(fn ($builderInner) => Query::searchFilter($builderInner, $this->searchFilter));
            }

            Query::sort($builder, self::$sort);

            foreach (self::$expand as $expand) {
                $builder->with($expand['relation'], function ($builderInner) use ($expand) {
                    $builderInner->select(array_map(fn ($field) => $expand['table'].'.'.$field, $expand['fields']));

                    if (! empty($expand['filter'])) {
                        Query::filter($builderInner, $expand['filter']);
                    }

                    if (! empty($expand['inFilter'])) {
                        Query::inFilter($builderInner, $expand['inFilter']);
                    }

                    if (! empty($expand['betweenFilter'])) {
                        Query::betweenFilter($builderInner, $expand['betweenFilter']);
                    }

                    if (! empty($expand['searchFilter'])) {
                        Query::searchFilter($builderInner, $expand['searchFilter']);
                    }

                    Query::sort($builderInner, $expand['sort'], $expand['table']);
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
