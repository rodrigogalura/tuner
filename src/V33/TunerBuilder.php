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

    public static function getInstance()
    {
        return new self(...func_get_args());
    }

    public function project(Request $request, array $projectableColumns)
    {
        if (! empty($request())) {
            if ($projection = $request->getProjection()) {
                $definedColumns = $this->builder->getQuery()->columns ?? ['*'];

                $projector = new Projector(
                    new $projection(
                        new ProjectableColumns($projectableColumns, $this->visibleColumns),
                        new DefinedColumns($definedColumns, $this->visibleColumns),
                        $request()
                    )
                );

                $this->projectedColumns = $projector->getProjectedColumns();
            }
        }

        return $this;
    }

    public function sort(Request $request, array $sortableColumns)
    {
        if (! empty($request())) {

        }

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
