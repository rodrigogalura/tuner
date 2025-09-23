<?php

namespace Tuner;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Tuner\Columns\ExpandableRelations;
use Tuner\Columns\FilterableColumns;
use Tuner\Columns\ProjectableColumns;
use Tuner\Columns\SearchableColumns;
use Tuner\Columns\SortableColumns;
use Tuner\Requests\ExpansionRequest;
use Tuner\Requests\FilterRequest;
use Tuner\Requests\LimitRequest;
use Tuner\Requests\PaginationRequest;
use Tuner\Requests\ProjectionRequest;
use Tuner\Requests\SearchRequest;
use Tuner\Requests\SortRequest;

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

    protected function getSearchableColumns(): array
    {
        return ['*'];
    }

    protected function getFilterableColumns(): array
    {
        return ['*'];
    }

    protected function getExpandableRelations(): array
    {
        return [];

        /*
            return [
                '[relation]' => [
                    // 'table' => '[table]',
                    // 'fk' => '[foreign_key]',
                    'options' => [
                        'projectable_columns' => ['*'],
                        'sortable_columns' => ['*'],
                        'searchable_columns' => ['*'],
                        'filterable_columns' => ['*'],
                    ],
                ],
            ];
         */
    }

    protected function limitable(): bool
    {
        return true;
    }

    protected function paginatable(): bool
    {
        return true;
    }

    /**
     * @return void
     */
    public function scopeSend(Builder $builder): Collection|LengthAwarePaginator
    {
        $tuner = new Tuner($builder, $request = $_GET, $this);
        if (empty($visibleColumns = $tuner->visibleColumns)) {
            return $builder->get();
        }

        [$config, $definedColumns] = [config('tuner'), $builder->getQuery()->columns ?? ['*']];

        $projectionBinder = fn (): ProjectionRequest => new ProjectionRequest($request, $config[Tuner::CONFIG_PROJECTION], $visibleColumns, $this->getProjectableColumns(), $definedColumns);
        $sortBinder = fn (): SortRequest => new SortRequest($request, $config[Tuner::CONFIG_SORT], $visibleColumns, $this->getSortableColumns());
        $searchBinder = fn (): SearchRequest => new SearchRequest($request, $config[Tuner::CONFIG_SEARCH], $visibleColumns, $this->getSearchableColumns());
        $filterBinder = fn (): FilterRequest => new FilterRequest($request, $config[Tuner::CONFIG_FILTER], $visibleColumns, $this->getFilterableColumns());
        $expansionBinder = fn (): ExpansionRequest => new ExpansionRequest($request, $config, $this, $definedColumns, $this->getExpandableRelations());
        $limitBinder = fn (): LimitRequest => new LimitRequest($request, $config[Tuner::CONFIG_LIMIT], $this->limitable());
        $paginationBinder = fn (): PaginationRequest => new PaginationRequest($request, $config[Tuner::CONFIG_PAGINATION], $this->paginatable());

        $tunerBuilder = $tuner->getBuilder();

        $container = [
            'projection' => [
                'bind' => fn ($requestContainer): ProjectionRequest => $projectionBinder(),
                'resolve' => fn ($projectionRequest): TunerBuilder => $tunerBuilder->project($projectionRequest),
            ],
            'sort' => [
                'bind' => fn ($requestContainer): SortRequest => $sortBinder(),
                'resolve' => fn ($sortRequest): TunerBuilder => $tunerBuilder->sort($sortRequest),
            ],
            'search' => [
                'bind' => fn ($requestContainer): SearchRequest => $searchBinder(),
                'resolve' => fn ($searchRequest): TunerBuilder => $tunerBuilder->search($searchRequest),
            ],
            'filter' => [
                'bind' => fn ($requestContainer): FilterRequest => $filterBinder(),
                'resolve' => fn ($filterRequest): TunerBuilder => $tunerBuilder->filter($filterRequest),
            ],
            'expansion' => [
                'bind' => fn ($requestContainer): ExpansionRequest => $expansionBinder(),
                'resolve' => fn ($expansionRequest, $expandableRelations): TunerBuilder => $tunerBuilder->expand($expansionRequest, $config, $expandableRelations),
            ],
            'limit' => [
                'bind' => fn ($requestContainer): LimitRequest => $limitBinder(),
                'resolve' => fn ($limitRequest): TunerBuilder => $tunerBuilder->limit($limitRequest),
            ],
            'pagination' => [
                'bind' => fn ($requestContainer): PaginationRequest => $paginationBinder(),
                'resolve' => fn ($paginationRequest): TunerBuilder => $tunerBuilder->paginate($paginationRequest),
            ],
        ];

        $requestContainer = RequestsContainer::create();

        foreach ($container as $key => $factories) {
            try {
                $requestContainer->bind($key, $factories['bind']);
                $requestContainer->resolveAndRunCallbackWhenRequested($key, $factories['resolve']);
            } catch (TunerException|ClientException $e) {
                $code = $e->getCode();

                $isDisabled =
                    is_a($e, TunerException::class) &&
                    in_array($code, [
                        ProjectableColumns::ERR_CODE_DISABLED,
                        SortableColumns::ERR_CODE_DISABLED,
                        SearchableColumns::ERR_CODE_DISABLED,
                        FilterableColumns::ERR_CODE_DISABLED,
                        ExpandableRelations::ERR_CODE_DISABLED,
                    ]);

                if (! $isDisabled) {
                    return new Collection([
                        'status' => 'error',
                        'code' => $code,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $tunerBuilder->build();
    }
}
