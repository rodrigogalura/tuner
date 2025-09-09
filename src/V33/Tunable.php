<?php

namespace RodrigoGalura\Tuner\V33;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use RodrigoGalura\Tuner\V33\ValueObjects\FilterableColumns;
use RodrigoGalura\Tuner\V33\ValueObjects\ProjectableColumns;
use RodrigoGalura\Tuner\V33\ValueObjects\Requests\FilterRequest;
use RodrigoGalura\Tuner\V33\ValueObjects\Requests\LimitRequest;
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

    protected function getFilterableColumns(): array
    {
        return ['*'];
    }

    protected function limitable(): bool
    {
        return true;
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

        $filterBinder = function () use ($request) {
            return new FilterRequest(
                config('tuner.'.Tuner::DIRECTIVE_FILTER),
                $request,
                $this->visibleColumns,
                $this->getFilterableColumns(),
            );
        };

        $limitBinder = function () use ($request) {
            return new LimitRequest(
                config('tuner.'.Tuner::DIRECTIVE_LIMIT),
                $request,
                $this->limitable(),
            );
        };

        // $expansionBinder = function () use ($request) {
        //     return new ExpansionRequest(
        //         config('tuner.'.Tuner::DIRECTIVE_EXPANSION),
        //         $request,
        //         $this->visibleColumns,
        //         $this->getProjectableColumns(),
        //     );
        // };

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
            'filter' => [
                'bind' => fn ($requestContainer): FilterRequest => $filterBinder(),
                'resolve' => fn ($request) => $tunerBuilder->filter($request),
            ],
            'limit' => [
                'bind' => fn ($requestContainer): LimitRequest => $limitBinder(),
                'resolve' => fn ($request) => $tunerBuilder->limit($request),
            ],
            // 'expand' => [
            //     'bind' => fn ($requestContainer): ExpansionRequest => $expansionBinder(),
            //     'resolve' => fn ($request) => $tunerBuilder->expand($request),
            // ],
        ];

        $requestContainer = RequestsContainer::create();

        foreach ($container as $key => $factories) {
            try {
                $requestContainer->bind($key, $factories['bind']);
                $requestContainer->resolveAndRunCallbackWhenRequested($key, $factories['resolve']);
            } catch (Exception $e) {
                switch ($code = $e->getCode()) {
                    case ProjectableColumns::ERR_CODE_DISABLED:
                    case SortableColumns::ERR_CODE_DISABLED:
                    case SearchableColumns::ERR_CODE_DISABLED:
                    case FilterableColumns::ERR_CODE_DISABLED:
                        // noop
                        break;

                    case Tuner::ERR_CODE_REQUEST_EXCEPTION: // todo: update this later
                        return new Collection([
                            'status' => 'error',
                            'code' => $code,
                            'message' => $e->getMessage(),
                        ]);

                    default:
                        throw $e;
                }
            }
        }

        return $tunerBuilder->build();
    }
}
