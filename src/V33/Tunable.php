<?php

namespace RodrigoGalura\Tuner\V33;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use RodrigoGalura\Tuner\V33\ValueObjects\ProjectableColumns;
use RodrigoGalura\Tuner\V33\ValueObjects\Requests\ProjectionRequest;
use RodrigoGalura\Tuner\V33\ValueObjects\Requests\SearchRequest;
use RodrigoGalura\Tuner\V33\ValueObjects\Requests\SortRequest;
use RodrigoGalura\Tuner\V33\ValueObjects\SearchableColumns;
use RodrigoGalura\Tuner\V33\ValueObjects\SortableColumns;

trait Tunable
{
    private readonly array $visibleColumns;

    private readonly array $definedColumns;

    protected function getProjectableColumns(): array
    {
        return ['*'];
    }

    protected function getSortableColumns(): array
    {
        return ['*'];
    }

    protected function getSearchableColumns(): array
    {
        return ['*'];
    }

    /**
     * @return void
     */
    public function scopeSend(Builder $builder): Collection
    {
        $this->visibleColumns = array_diff(
            $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable()),
            $this->getHidden()
        );

        $this->definedColumns = $builder->getQuery()->columns ?? ['*'];

        [$config, $request] = [config('tuner'), $_GET];

        $tunerBuilder = TunerBuilder::getInstance($builder, $this->visibleColumns, $request);

        $projectionBinder = function () use ($request) {
            return new ProjectionRequest(
                config('tuner.'.Tuner::DIRECTIVE_PROJECTION),
                $request,
                $this->visibleColumns,
                $this->getProjectableColumns(),
                $this->definedColumns,
            );
        };

        $searchBinder = function () use ($request) {
            return new SearchRequest(
                config('tuner.'.Tuner::DIRECTIVE_SEARCH),
                $request,
                $this->visibleColumns,
                $this->getSearchableColumns(),
            );
        };

        $sortBinder = function () use ($request) {
            return new SortRequest(
                config('tuner.'.Tuner::DIRECTIVE_SORT),
                $request,
                $this->visibleColumns,
                $this->getSortableColumns(),
            );
        };

        $container = [
            'project' => [
                'bind' => fn ($requestContainer): ProjectionRequest => $projectionBinder(),
                'resolve' => fn ($request) => $tunerBuilder->project($request),
            ],
            'search' => [
                'bind' => fn ($requestContainer): SearchRequest => $searchBinder(),
                'resolve' => fn ($request) => $tunerBuilder->search($request),
            ],
            'sort' => [
                'bind' => fn ($requestContainer): SortRequest => $sortBinder(),
                'resolve' => fn ($request) => $tunerBuilder->sort($request),
            ],
        ];

        $requestContainer = RequestsContainer::create();

        foreach ($container as $key => $factories) {
            try {
                $requestContainer->bind($key, $factories['bind']);
                $requestContainer->resolveAndRunCallbackWhenRequested($key, $factories['resolve']);
            } catch (Exception $e) {
                switch ($e->getCode()) {
                    case ProjectableColumns::ERR_CODE_DISABLED:
                    case SortableColumns::ERR_CODE_DISABLED:
                    case SearchableColumns::ERR_CODE_DISABLED:
                        // noop
                        break;

                    default:
                        throw $e;
                }
            }
        }

        return $tunerBuilder->build();
    }
}
