<?php

namespace RodrigoGalura\Tuner\V33;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use RodrigoGalura\Tuner\V33\ValueObjects\Requests\LimitRequest;

final class TunerBuilder
{
    use HasSingleton;

    private readonly ?array $projection;

    private readonly ?array $search;

    private readonly ?array $sort;

    private readonly ?array $filter;

    private readonly ?array $limit;

    private readonly ?array $pagination;

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

    // public function expand(Request $request)
    // {
    //     $this->expand = $request();

    //     return $this;
    // }

    public function build(): Collection|LengthAwarePaginator
    {
        if ($this->wasAssigned('projection')) {
            if (empty($projectedColumns = current($this->projection))) {
                return new Collection([]);
            }

            $this->builder->select($projectedColumns);
        }

        if ($this->wasAssigned('sort')) {
            $sort = current($this->sort);
            foreach ($sort as $column => $order) {
                $this->builder->orderBy($column, $order);
            }
        }

        if ($this->wasAssigned('search')) {
            $search = current($this->search);
            [$columns, $searchKeyword] = [key($search), current($search)];

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

        if ($this->wasAssigned('pagination')) {
            return $this->builder->paginate(current($this->pagination));
        }

        return $this->builder->get();
    }
}
