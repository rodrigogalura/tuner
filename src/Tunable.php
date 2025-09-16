<?php

namespace Tuner;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Tuner\Columns\FilterableColumns;
use Tuner\Columns\ProjectableColumns;
use Tuner\Columns\SearchableColumns;
use Tuner\Columns\SortableColumns;
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
        if (empty($visibleColumns = array_diff(
            $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable()),
            $this->getHidden()
        ))) {
            return $builder->get();
        }

        [$config, $request] = [config('tuner'), $_GET];

        $projectionBinder = fn (): ProjectionRequest => new ProjectionRequest($config[Tuner::CONFIG_PROJECTION], $request, $visibleColumns, $this->getProjectableColumns(), definedColumns: $builder->getQuery()->columns ?? ['*']);
        $sortBinder = fn (): SortRequest => new SortRequest($config[Tuner::CONFIG_SORT], $request, $visibleColumns, $this->getSortableColumns());
        $searchBinder = fn (): SearchRequest => new SearchRequest($config[Tuner::CONFIG_SEARCH], $request, $visibleColumns, $this->getSearchableColumns());
        $filterBinder = fn (): FilterRequest => new FilterRequest($config[Tuner::CONFIG_FILTER], $request, $visibleColumns, $this->getFilterableColumns());
        $limitBinder = fn (): LimitRequest => new LimitRequest($config[Tuner::CONFIG_LIMIT], $request, $this->limitable());
        $paginationBinder = fn (): PaginationRequest => new PaginationRequest($config[Tuner::CONFIG_PAGINATION], $request, $this->paginatable());

        $tunerBuilder = TunerBuilder::getInstance($builder, $request);

        $container = [
            'project' => [
                'bind' => fn ($requestContainer): ProjectionRequest => $projectionBinder(),
                'resolve' => fn ($request): TunerBuilder => $tunerBuilder->project($request),
            ],
            'sort' => [
                'bind' => fn ($requestContainer): SortRequest => $sortBinder(),
                'resolve' => fn ($request): TunerBuilder => $tunerBuilder->sort($request),
            ],
            'search' => [
                'bind' => fn ($requestContainer): SearchRequest => $searchBinder(),
                'resolve' => fn ($request): TunerBuilder => $tunerBuilder->search($request),
            ],
            'filter' => [
                'bind' => fn ($requestContainer): FilterRequest => $filterBinder(),
                'resolve' => fn ($request): TunerBuilder => $tunerBuilder->filter($request),
            ],
            'limit' => [
                'bind' => fn ($requestContainer): LimitRequest => $limitBinder(),
                'resolve' => fn ($request): TunerBuilder => $tunerBuilder->limit($request),
            ],
            'pagination' => [
                'bind' => fn ($requestContainer): PaginationRequest => $paginationBinder(),
                'resolve' => fn ($request): TunerBuilder => $tunerBuilder->paginate($request),
            ],
        ];

        $requestContainer = RequestsContainer::create();

        foreach ($container as $key => $factories) {
            try {
                $requestContainer->bind($key, $factories['bind']);
                $requestContainer->resolveAndRunCallbackWhenRequested($key, $factories['resolve']);
            } catch (TunerException $e) {
                switch ($code = $e->getCode()) {
                    case ProjectableColumns::ERR_CODE_DISABLED:
                    case SortableColumns::ERR_CODE_DISABLED:
                    case SearchableColumns::ERR_CODE_DISABLED:
                    case FilterableColumns::ERR_CODE_DISABLED:
                        // noop
                        break;

                    default:
                        throw $e;
                }
            } catch (ClientException $e) {
                return new Collection([
                    'status' => 'error',
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return $tunerBuilder->build();
    }
}
