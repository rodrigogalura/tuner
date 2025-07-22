<?php

namespace Laradigs\Tweaker;

use Illuminate\Database\Eloquent\Builder;
use Laradigs\Tweaker\Projection\ExceptProjection;
use Laradigs\Tweaker\Projection\IntersectProjection;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;
use Laradigs\Tweaker\Projection\Projection;
use Laradigs\Tweaker\Search\Search;

use function RGalura\ApiIgniter\filter_explode;

/**
 * Singleton
 */
final class TweakerBuilder
{
    private ?array $projectedFields = null;

    private ?array $searchedResult = null;

    private ?array $sortedResult = null;

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    private function __construct(
        private Builder $builder,
        private array $columnListing,
        private array $config,
        private array $clientInput
    ) {
        //
    }

    /**
     * Singletons should not be cloneable.
     */
    protected function __clone() {}

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton.');
    }

    private function projectionWasExecute()
    {
        return ! is_null($this->projectedFields);
    }

    private function searchWasExecute()
    {
        return ! is_null($this->searchedResult);
    }

    public static function getInstance(
        Builder $builder,
        array $columnListing,
        array $config,
        array $clientInput
    ) {
        return new self($builder, $columnListing, $config, $clientInput);
    }

    public function projection(array $projectableFields)
    {
        $intersectKey = $this->config['projection']['intersect_key'];
        $exceptKey = $this->config['projection']['except_key'];

        $projection[$intersectKey] = new IntersectProjection(
            $this->columnListing,
            $projectableFields,
            definedFields: $this->builder->getQuery()->columns ?? ['*'],
            clientInput: [$intersectKey => $this->clientInput[$intersectKey] ?? null],
        );

        $projection[$exceptKey] = new ExceptProjection(
            $this->columnListing,
            $projectableFields,
            definedFields: $this->builder->getQuery()->columns ?? ['*'],
            clientInput: [$exceptKey => $this->clientInput[$exceptKey] ?? null],
        );

        if ($key = Projection::getKeyCanUse()) {
            try {
                $this->projectedFields = $projection[$key]->project();
            } catch (NoActionWillPerformException $e) {
                //
            }
        }

        return $this;
    }

    public function searchFilter(array $searchableFields)
    {
        $key = $this->config['search']['key'];
        $minimumLength = $this->config['search']['minimum_length'];

        $search = new Search(
            $this->columnListing,
            $searchableFields,
            clientInput: [$key => $this->clientInput[$key] ?? []],
            minimumLength: $minimumLength
        );

        try {
            $this->searchedResult = $search->search();
        } catch (NoActionWillPerformException $e) {
            //
        }

        return $this;
    }

    public function sort(array $sortableFields)
    {
        // $sort = new Sort(
        //     model: $this->model,
        //     sortableFields: $sortableFields,
        //     clientInput: $this->clientInput,
        //     sortConfig: $this->config['sort']
        // );

        // if ($sort->isUsed()) {
        //     try {
        //         $this->sortedResult = $sort->sort();
        //     } catch (NoActionWillPerformException $e) {
        //         //
        //     }
        // }

        return $this;
    }

    public function execute()
    {
        if ($this->projectionWasExecute()) {
            if (empty($this->projectedFields)) {
                return [];
            }

            $this->builder->select($this->projectedFields);
        }

        if ($this->searchWasExecute()) {
            $searchFromFields = filter_explode(key($this->searchedResult));
            $searchKeyword = current($this->searchedResult);

            $this->builder->where(fn ($builderInner) => $builderInner->whereAny($searchFromFields, 'LIKE', $searchKeyword));
        }

        return $this->builder->get();
    }
}
