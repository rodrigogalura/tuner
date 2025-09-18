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

    private function buildExpansion(array $expansion): void
    {
        dd($expansion);
        // return $this->builder->paginate($pageSize);
    }

    public function __call(string $attribute, array $arguments)
    {
        $request = array_shift($arguments);

        $attributes = [
            'project' => 'projection',
            'paginate' => 'pagination',
            'expand' => 'expansion',
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

        if ($this->wasAssigned('limit')) {
            $this->buildLimit($this->limit);
        }

        if ($this->wasAssigned('pagination')) {
            return $this->buildPagination(current($this->pagination));
        }

        if ($this->wasAssigned('expansion')) {
            return $this->buildExpansion(current($this->expansion));
        }

        return $this->builder->get();
    }

    public static function create()
    {
        self::addInstance($instance = new self(...func_get_args()));

        return $instance;
    }
}
