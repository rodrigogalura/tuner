<?php

namespace Laradigs\Tweaker\V33;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Laradigs\Tweaker\V32\HasSingleton;
use Laradigs\Tweaker\V33\Projection\Projector;
use Laradigs\Tweaker\V33\ValueObjects\DefinedColumns;
use Laradigs\Tweaker\V33\ValueObjects\ProjectableColumns;
use Laradigs\Tweaker\V33\ValueObjects\Requests\RequestInterface as Request;

final class TunerBuilder
{
    use HasSingleton;

    private readonly ?array $projectedColumns;

    /**
     * Private constructor
     */
    private function __construct(
        private Builder $builder,
        private array $visibleColumns,
    ) {
        //
    }

    public static function getInstance()
    {
        return new self(...func_get_args());
    }

    public function project(Request $request, array $projectableColumns)
    {
        if ($projection = $request->getProjection()) {
            $definedColumns = $this->builder->getQuery()->columns ?? ['*'];

            $projector = new Projector(
                new $projection(
                    new ProjectableColumns($projectableColumns, $this->visibleColumns),
                    new DefinedColumns($definedColumns, $this->visibleColumns),
                    // new Columns($request(), $this->visibleColumns)
                    $request()
                )
            );

            $this->projectedColumns = $projector->getProjectedColumns();
        }

        return $this;
    }

    public function sort(Request $request, array $sortableColumns)
    {
        return $this;
    }

    public function build()
    {
        // Check if getProjectedColumns() was executed
        if (! is_null($this->projectedColumns ?? null)) {

            if (empty($this->projectedColumns)) {
                return new Collection([]);
            }

            $this->builder->select($this->projectedColumns);
        }

        return $this->builder->get();
    }
}
