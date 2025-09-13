<?php

namespace Tuner;

use Exception;
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
        $visibleColumns = array_diff(
            $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable()),
            $this->getHidden()
        );

        $tunerBuilder = TunerBuilder::getInstance($builder, $visibleColumns, $request = $_GET);

        $config = config('tuner');

        $projectionBinder = fn () => new ProjectionRequest($config[Tuner::CONFIG_PROJECTION], $request, $visibleColumns, $this->getProjectableColumns(), definedColumns: $builder->getQuery()->columns ?? ['*']);
        $sortBinder = fn () => new SortRequest($config[Tuner::CONFIG_SORT], $request, $visibleColumns, $this->getSortableColumns());
        $searchBinder = fn () => new SearchRequest($config[Tuner::CONFIG_SEARCH], $request, $visibleColumns, $this->getSearchableColumns());
        $filterBinder = fn () => new FilterRequest($config[Tuner::CONFIG_FILTER], $request, $visibleColumns, $this->getFilterableColumns());
        $limitBinder = fn () => new LimitRequest($config[Tuner::CONFIG_LIMIT], $request, $this->limitable());
        $paginationBinder = fn () => new PaginationRequest($config[Tuner::CONFIG_PAGINATION], $request, $this->paginatable());

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
