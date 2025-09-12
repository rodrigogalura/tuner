<?php

namespace Tuner\Tuner\V33;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Tuner\Tuner\V33\ValueObjects\FilterableColumns;
use Tuner\Tuner\V33\ValueObjects\ProjectableColumns;
use Tuner\Tuner\V33\ValueObjects\Requests\FilterRequest;
use Tuner\Tuner\V33\ValueObjects\Requests\LimitRequest;
use Tuner\Tuner\V33\ValueObjects\Requests\PaginationRequest;
use Tuner\Tuner\V33\ValueObjects\Requests\ProjectionRequest;
use Tuner\Tuner\V33\ValueObjects\Requests\SearchRequest;
use Tuner\Tuner\V33\ValueObjects\Requests\SortRequest;
use Tuner\Tuner\V33\ValueObjects\SearchableColumns;
use Tuner\Tuner\V33\ValueObjects\SortableColumns;

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

    protected function paginatable(): bool
    {
        return true;
    }

    /**
     * @return void
     */
    public function scopeSend(Builder $builder): Collection|LengthAwarePaginator
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
                config('tuner.'.Tuner::CONFIG_PROJECTION),
                $request,
                $this->visibleColumns,
                $this->getProjectableColumns(),
                $this->definedColumns,
            );
        };

        $sortBinder = function () use ($request) {
            return new SortRequest(
                config('tuner.'.Tuner::CONFIG_SORT),
                $request,
                $this->visibleColumns,
                $this->getSortableColumns(),
            );
        };

        $searchBinder = function () use ($request) {
            return new SearchRequest(
                config('tuner.'.Tuner::CONFIG_SEARCH),
                $request,
                $this->visibleColumns,
                $this->getSearchableColumns(),
            );
        };

        $filterBinder = function () use ($request) {
            return new FilterRequest(
                config('tuner.'.Tuner::CONFIG_FILTER),
                $request,
                $this->visibleColumns,
                $this->getFilterableColumns(),
            );
        };

        $limitBinder = function () use ($request) {
            return new LimitRequest(
                config('tuner.'.Tuner::CONFIG_LIMIT),
                $request,
                $this->limitable(),
            );
        };

        $paginationBinder = function () use ($request) {
            return new PaginationRequest(
                config('tuner.'.Tuner::CONFIG_PAGINATION),
                $request,
                $this->paginatable(),
            );
        };

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
