<?php

namespace RodrigoGalura\Tuner\V33;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use RodrigoGalura\Tuner\V33\ValueObjects\Requests\ProjectionRequest;
use RodrigoGalura\Tuner\V33\ValueObjects\Requests\SortRequest;

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

        $definedColumns = $builder->getQuery()->columns ?? ['*'];




        [$config, $request] = [config('tuner'), $_GET];

        $tunerBuilder = TunerBuilder::getInstance($builder, $visibleColumns, $request);

        $projectionBinder = function() use($config, $visibleColumns, $definedColumns, $request) {
            return new ProjectionRequest(
                $config[Tuner::DIRECTIVE_PROJECTION][Tuner::PARAM_KEY],
                $visibleColumns,
                $this->getProjectableColumns(),
                $definedColumns,
                $request
            );
        };

        $container = [
            'project' => [
                'bind' => fn ($requestContainer) => $projectionBinder(),
                'resolve' => fn ($request) => $tunerBuilder->project($request),
            ],
            'sort' => [
                'bind' => fn ($requestContainer): SortRequest => new SortRequest($config[Tuner::DIRECTIVE_SORT][Tuner::PARAM_KEY], $visibleColumns, $request),
                'resolve' => fn ($request) => $tunerBuilder->sort($request, $this->getSortableColumns()),
            ],
        ];

        $requestContainer = RequestsContainer::create();

        foreach ($container as $key => $factories) {
            $requestContainer->bind($key, $factories['bind']);
            $requestContainer->resolveAndRunCallbackWhenRequested($key, $factories['resolve']);
        }

        return $tunerBuilder->build();
    }
}
