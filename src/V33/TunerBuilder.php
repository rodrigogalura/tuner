<?php

namespace RodrigoGalura\Tuner\V33;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use RodrigoGalura\Tuner\V33\ValueObjects\Requests\LimitRequest;
use RodrigoGalura\Tuner\V33\ValueObjects\Requests\RequestInterface as Request;

final class TunerBuilder
{
    use HasSingleton;

    private readonly ?array $projectedColumns;

    private readonly ?array $search;

    private readonly ?array $sort;

    private readonly ?array $filter;

    private readonly ?array $expand;

    /**
     * Private constructor
     */
    private function __construct(
        private Builder $builder,
        private array $visibleColumns,
    ) {
        //
    }

    private function wasAssigned($property)
    {
        return ! is_null($this->{$property} ?? null);
    }

    public static function getInstance()
    {
        return new self(...func_get_args());
    }

    public function project(Request $request)
    {
        $this->projectedColumns = $request();

        return $this;
    }

    public function search(Request $request)
    {
        $this->search = $request();

        return $this;
    }

    public function sort(Request $request)
    {
        $this->sort = $request();

        return $this;
    }

    public function filter(Request $request)
    {
        $this->filter = $request();

        return $this;
    }

    public function limit(Request $request)
    {
        $this->limit = $request();

        return $this;
    }

    public function expand(Request $request)
    {
        $this->expand = $request();

        return $this;
    }

    public function build()
    {
        if ($this->wasAssigned('projectedColumns')) {
            if (empty($this->projectedColumns)) {
                return new Collection([]);
            }

            $this->builder->select($this->projectedColumns);
        }

        if ($this->wasAssigned('sort')) {
            foreach ($this->sort as $column => $order) {
                $this->builder->orderBy($column, $order);
            }
        }

        if ($this->wasAssigned('search')) {
            [$columns, $searchKeyword] = [key($this->search), current($this->search)];

            $this->builder->whereAny(explode(', ', $columns), 'LIKE', $searchKeyword);
        }

        if ($this->wasAssigned('filter')) {
            if ($filter = $this->filter['filter'] ?? null) {
                foreach ($filter as [$logicalOperator, $column, $not, $comparisonOperator, $val]) {
                    $this->builder->where($column, $comparisonOperator, $val, $logicalOperator.($not ? ' NOT' : ''));
                }
            }

            if ($inFilter = $this->filter['in'] ?? null) {
                foreach ($inFilter as [$logicalOperator, $column, $not, $val]) {
                    $this->builder->whereIn($column, $val, $logicalOperator, $not);
                }
            }

            if ($betweenFilter = $this->filter['between'] ?? null) {
                foreach ($betweenFilter as [$logicalOperator, $column, $not, $val]) {
                    $this->builder->whereBetween($column, $val, $logicalOperator, $not);
                }
            }
        }

        if ($this->wasAssigned('limit')) {
            $this->builder->limit($this->limit[LimitRequest::KEY_LIMIT]);

            if ($offset = $this->limit[LimitRequest::KEY_OFFSET] ?? null) {
                $this->builder->offset($offset);
            }
        }

        return $this->builder->get();
    }
}
