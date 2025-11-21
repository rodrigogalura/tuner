<?php

namespace Tuner;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Tuner\Fields\ExpandableRelations;
use Tuner\Fields\FilterableFields;
use Tuner\Fields\ProjectableFields;
use Tuner\Fields\SearchableFields;
use Tuner\Fields\SortableFields;
use Tuner\Requests\ExpansionRequest;
use Tuner\Requests\FilterRequest;
use Tuner\Requests\LimitRequest;
use Tuner\Requests\PaginationRequest;
use Tuner\Requests\ProjectionRequest;
use Tuner\Requests\SearchRequest;
use Tuner\Requests\SortRequest;

trait Tunable
{
    protected function getProjectableFields(): array
    {
        return ['*'];
    }

    protected function getSortableFields(): array
    {
        return ['*'];
    }

    protected function getSearchableFields(): array
    {
        return ['*'];
    }

    protected function getFilterableFields(): array
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
                        'projectable_fields' => ['*'],
                        'sortable_fields' => ['*'],
                        'searchable_fields' => ['*'],
                        'filterable_fields' => ['*'],
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
        if (empty($visibleFields = $tuner->visibleFields)) {
            return $builder->get();
        }

        [$config, $definedFields] = [config('tuner'), $builder->getQuery()->columns ?? ['*']];

        $projectionBinder = fn (): ProjectionRequest => new ProjectionRequest($request, $config[Tuner::CONFIG_PROJECTION], $visibleFields, $this->getProjectableFields(), $definedFields);
        $sortBinder = fn (): SortRequest => new SortRequest($request, $config[Tuner::CONFIG_SORT], $visibleFields, $this->getSortableFields());
        $searchBinder = fn (): SearchRequest => new SearchRequest($request, $config[Tuner::CONFIG_SEARCH], $visibleFields, $this->getSearchableFields());
        $filterBinder = fn (): FilterRequest => new FilterRequest($request, $config[Tuner::CONFIG_FILTER], $visibleFields, $this->getFilterableFields());
        $expansionBinder = fn (): ExpansionRequest => new ExpansionRequest($request, $config, $this, $definedFields, $this->getExpandableRelations());
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
                        ProjectableFields::ERR_CODE_DISABLED,
                        SortableFields::ERR_CODE_DISABLED,
                        SearchableFields::ERR_CODE_DISABLED,
                        FilterableFields::ERR_CODE_DISABLED,
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
