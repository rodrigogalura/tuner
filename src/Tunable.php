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

    // protected function getExpandableRelations(): array
    // {
    //     # disable by default
    //     return [];
    // }

    protected function getExpandableRelations(): array
    {
        return [
            'phone' => [
                'projectable_columns' => ['*'],
                'sortable_columns' => ['*'],
                'searchable_columns' => ['*'],
                'filterable_columns' => ['*'],

                // Check if possible
                // 'limitable' => true,
                // 'paginatable' => true,
            ],
        ];
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

        [$tunerBuilder, $config] = [$tuner->getBuilder(), config('tuner')];

        $projectionBinder = fn (): ProjectionRequest => new ProjectionRequest($request, $config[Tuner::CONFIG_PROJECTION], $visibleColumns, $this->getProjectableColumns(), $definedColumns = $builder->getQuery()->columns ?? ['*']);
        $sortBinder = fn (): SortRequest => new SortRequest($request, $config[Tuner::CONFIG_SORT], $visibleColumns, $this->getSortableColumns());
        $searchBinder = fn (): SearchRequest => new SearchRequest($request, $config[Tuner::CONFIG_SEARCH], $visibleColumns, $this->getSearchableColumns());
        $filterBinder = fn (): FilterRequest => new FilterRequest($request, $config[Tuner::CONFIG_FILTER], $visibleColumns, $this->getFilterableColumns());
        $limitBinder = fn (): LimitRequest => new LimitRequest($request, $config[Tuner::CONFIG_LIMIT], $this->limitable());
        $paginationBinder = fn (): PaginationRequest => new PaginationRequest($request, $config[Tuner::CONFIG_PAGINATION], $this->paginatable());

        $expansionBinder = fn (): ExpansionRequest => new ExpansionRequest($request, $config, $this, $builder, $visibleColumns, $this->getExpandableRelations());
        // $expansionBinder = fn (): ExpansionRequest => new ExpansionRequest($config[Tuner::CONFIG_EXPANSION], $request, $this, $visibleColumns, $this->getExpandableRelations());

        // $expansionBinder = function() use ($definedColumns) : ExpansionRequest {
        //     new ExpansionRequest(
        //         $config[Tuner::CONFIG_EXPANSION],
        //         $request,
        //         $this,
        //         fn (): ProjectionRequest => new ProjectionRequest($config[Tuner::CONFIG_PROJECTION], $request, $visibleColumns, $this->getProjectableColumns(), $definedColumns)
        //     );
        // };

        $container = [
            // 'project' => [
            //     'bind' => fn ($requestContainer): ProjectionRequest => $projectionBinder(),
            //     'resolve' => fn ($request): TunerBuilder => $tunerBuilder->project($request),
            // ],
            // 'sort' => [
            //     'bind' => fn ($requestContainer): SortRequest => $sortBinder(),
            //     'resolve' => fn ($request): TunerBuilder => $tunerBuilder->sort($request),
            // ],
            // 'search' => [
            //     'bind' => fn ($requestContainer): SearchRequest => $searchBinder(),
            //     'resolve' => fn ($request): TunerBuilder => $tunerBuilder->search($request),
            // ],
            // 'filter' => [
            //     'bind' => fn ($requestContainer): FilterRequest => $filterBinder(),
            //     'resolve' => fn ($request): TunerBuilder => $tunerBuilder->filter($request),
            // ],
            // 'limit' => [
            //     'bind' => fn ($requestContainer): LimitRequest => $limitBinder(),
            //     'resolve' => fn ($request): TunerBuilder => $tunerBuilder->limit($request),
            // ],
            // 'pagination' => [
            //     'bind' => fn ($requestContainer): PaginationRequest => $paginationBinder(),
            //     'resolve' => fn ($request): TunerBuilder => $tunerBuilder->paginate($request),
            // ],
            'expansion' => [
                'bind' => fn ($requestContainer): ExpansionRequest => $expansionBinder(),
                'resolve' => fn ($request): TunerBuilder => $tunerBuilder->expand($request),
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
