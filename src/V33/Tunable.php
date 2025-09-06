<?php

namespace Laradigs\Tweaker\V33;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Laradigs\Tweaker\V33\ValueObjects\Requests\ProjectionRequest;

trait Tunable
{
    protected function getProjectableColumns(): array
    {
        return ['*'];
    }

    protected function getSortableColumns(): array
    {
        return ['*'];
    }

    /**
     * @return void
     */
    public function scopeSend(Builder $builder): Collection
    {
        $visibleColumns = array_diff(
            $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable()),
            $this->getHidden()
        );

        [$config, $request] = [config('tweaker'), $_GET];

        $key = Tuner::PARAM_KEY;

        $projectionRequest = new ProjectionRequest($config[Tuner::DIRECTIVE_PROJECTION][$key], $request);

        return TunerBuilder::getInstance($builder, $visibleColumns, $request)
            ->project($projectionRequest, $this->getProjectableColumns())
            // ->sort($this->getSortableColumns())
            ->build();
    }
}
