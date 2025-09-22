<?php

namespace Tuner;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Tuner\Requests\FilterRequest;
use Tuner\Requests\LimitRequest;

/**
 * @internal
 */
final class TunerBuilder
{
    use HasSingleton;

    private readonly ?array $projection;

    private readonly ?array $search;

    private readonly ?array $sort;

    private readonly ?array $filter;

    private readonly ?array $limit;

    private readonly ?array $pagination;

    private readonly ?array $expansion;

    /**
     * Private constructor
     */
    private function __construct(
        private Builder $builder,
    ) {
        //
    }

    private function wasAssigned($property)
    {
        return ! is_null($this->{$property} ?? null);
    }

    private function buildProjection(array $projectedColumns): void
    {
        $this->builder->select($projectedColumns);
    }

    private function buildSort(array $sort): void
    {
        foreach ($sort as $column => $order) {
            $this->builder->orderBy($column, $order);
        }
    }

    private function buildSearch(array $search): void
    {
        [$columns, $searchKeyword] = [key($search), current($search)];

        $this->builder->where(fn ($builder) => $builder->whereAny(explode(', ', $columns), 'LIKE', $searchKeyword));
    }

    private function buildFilter(array $filters): void
    {
        if ($filter = $filters[FilterRequest::KEY_FILTER] ?? null) {
            $this->builder->where(function ($builder) use ($filter): void {
                foreach ($filter as [$logicalOperator, $column, $not, $comparisonOperator, $val]) {
                    $builder->where($column, $comparisonOperator, $val, $logicalOperator.($not ? ' NOT' : ''));
                }
            });
        }

        if ($inFilter = $filters[FilterRequest::KEY_IN] ?? null) {
            $this->builder->where(function ($builder) use ($inFilter): void {
                foreach ($inFilter as [$logicalOperator, $column, $not, $val]) {
                    $builder->whereIn($column, $val, $logicalOperator, $not);
                }
            });
        }

        if ($betweenFilter = $filters[FilterRequest::KEY_BETWEEN] ?? null) {
            $this->builder->where(function ($builder) use ($betweenFilter): void {
                foreach ($betweenFilter as [$logicalOperator, $column, $not, $val]) {
                    $builder->whereBetween($column, $val, $logicalOperator, $not);
                }
            });
        }
    }

    private function buildExpansion(array $expansion): void
    {
        $expandKey = $expansion['config'][Tuner::CONFIG_EXPANSION][Tuner::PARAM_KEY];

        foreach ($expansion['request'][$expandKey] as $relation => $alias) {

            $this->builder->with($relation, function ($builder) use ($expansion, $relation, $alias): void {

                if ($settings = $expansion['expandableRelations'][$relation] ?? null) {
                    $table = $settings['table'];
                    $fk = $settings['fk'];

                    $keys = [
                        implode(',', $expansion['config'][Tuner::CONFIG_PROJECTION][Tuner::PARAM_KEY]) => function (array $args): void {
                            if (! in_array($args['fk'], $columns = $args['request'])) {
                                array_push($columns, $args['fk']);
                            }

                            $args['builder']->select(array_map(fn ($field): string => "{$args['table']}.{$field}", $columns));
                        },
                        $expansion['config'][Tuner::CONFIG_SORT][Tuner::PARAM_KEY] => function (array $args): void {
                            $sort = $args['request'];

                            foreach ($sort as $field => $direction) {
                                $args['builder']->orderBy("{$args['table']}.{$field}", $direction);
                            }
                        },
                        $expansion['config'][Tuner::CONFIG_SEARCH][Tuner::PARAM_KEY] => function (array $args): void {
                            $search = $args['request'];

                            $args['builder']->whereAny(explode_sanitize(key($search)), 'LIKE', current($search));
                        },
                    ];

                    foreach ($keys as $key => $action) {
                        $modifiers = explode(',', $key);

                        foreach ($modifiers as $modifier) {
                            if ($request = $expansion['request'][$alias.$expansion['config'][Tuner::CONFIG_EXPANSION]['separator'].$modifier] ?? null) {
                                $action(compact('request', 'builder', 'table', 'fk'));
                            }
                        }
                    }
                }
            });
        }
    }

    private function buildLimit(array $limit): void
    {
        $this->builder->limit($limit[LimitRequest::KEY_LIMIT]);

        if ($offset = $limit[LimitRequest::KEY_OFFSET] ?? null) {
            $this->builder->offset($offset);
        }
    }

    private function buildPagination(int $pageSize): LengthAwarePaginator
    {
        return $this->builder->paginate($pageSize);
    }

    public function expand(array $request, array $config, array $expandableRelations)
    {
        $this->expansion = compact('request', 'config', 'expandableRelations');

        return $this;
    }

    public function __call(string $attribute, array $arguments)
    {
        $request = array_shift($arguments);

        $attributes = [
            'project' => 'projection',
            'paginate' => 'pagination',
        ];

        $property = $attributes[$attribute] ?? null;
        $this->{$property ?? $attribute} = $request();

        return $this;
    }

    public function build(): Collection|LengthAwarePaginator
    {
        if ($this->wasAssigned('projection')) {
            if (empty($projectedColumns = current($this->projection))) {
                return new Collection([]);
            }

            $this->buildProjection($projectedColumns);
        }

        if ($this->wasAssigned('sort')) {
            $this->buildSort(current($this->sort));
        }

        if ($this->wasAssigned('search')) {
            $this->buildSearch(current($this->search));
        }

        if ($this->wasAssigned('filter')) {
            $this->buildFilter($this->filter);
        }

        if ($this->wasAssigned('expansion')) {
            $this->buildExpansion($this->expansion);
        }

        if ($this->wasAssigned('limit')) {
            $this->buildLimit($this->limit);
        }

        if ($this->wasAssigned('pagination')) {
            return $this->buildPagination(current($this->pagination));
        }

        return $this->builder->get();
    }

    public static function create()
    {
        self::addInstance($instance = new self(...func_get_args()));

        return $instance;
    }
}
