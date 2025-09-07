<?php

namespace RodrigoGalura\Tuner\V33;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use RodrigoGalura\Tuner\V33\Projection\Projector;
use RodrigoGalura\Tuner\V33\ValueObjects\DefinedColumns;
use RodrigoGalura\Tuner\V33\ValueObjects\ProjectableColumns;
use RodrigoGalura\Tuner\V33\ValueObjects\Requests\RequestInterface as Request;

final class TunerBuilder
{
    use HasSingleton;

    private readonly ?array $projectedColumns;

    private readonly ?array $sort;

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

    public function sort(Request $request, array $sortableColumns)
    {
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

        return $this->builder->get();
    }
}
