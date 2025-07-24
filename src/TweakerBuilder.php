<?php

namespace Laradigs\Tweaker;

use Illuminate\Database\Eloquent\Builder;
use Laradigs\Tweaker\Projection\ExceptProjection;
use Laradigs\Tweaker\Projection\IntersectProjection;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;
use Laradigs\Tweaker\Projection\Projection;
use Laradigs\Tweaker\Search\Search;
use Laradigs\Tweaker\Sort\Sort;

/**
 * Singleton
 */
final class TweakerBuilder
{
    private ?array $projectedFields = null;

    private ?array $searchedResult = null;

    private ?array $sortedResult = null;

    private ?array $projection = null;

    private ?Sort $sort = null;

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    private function __construct(
        private Builder $builder,
        private array $visibleFields,
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

    private function sortWasExecute()
    {
        return ! is_null($this->sortedResult);
    }

    public static function getInstance(
        Builder $builder,
        array $visibleFields,
        array $config,
        array $clientInput
    ) {
        return new self($builder, $visibleFields, $config, $clientInput);
    }

    public function projection(array $projectableFields)
    {
        $intersectKey = $this->config['projection']['intersect_key'];
        $exceptKey = $this->config['projection']['except_key'];

        $this->projection[$intersectKey] = new IntersectProjection(
            $this->visibleFields,
            $projectableFields,
            definedFields: $this->builder->getQuery()->columns ?? ['*'],
            clientInput: [$intersectKey => $this->clientInput[$intersectKey] ?? null],
        );

        $this->projection[$exceptKey] = new ExceptProjection(
            $this->visibleFields,
            $projectableFields,
            definedFields: $this->builder->getQuery()->columns ?? ['*'],
            clientInput: [$exceptKey => $this->clientInput[$exceptKey] ?? null],
        );

        // if ($key = Projection::getKeyCanUse()) {
        //     try {
        //         $this->projectedFields = $projection[$key]->project();
        //     } catch (NoActionWillPerformException $e) {
        //         //
        //     }
        // }

        return $this;
    }

    public function searchFilter(array $searchableFields)
    {
        $key = $this->config['search']['key'];
        $minimumLength = $this->config['search']['minimum_length'];

        $search = new Search(
            $this->visibleFields,
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
        $key = $this->config['sort']['key'];

        $this->sort = new Sort(
            $this->visibleFields,
            $sortableFields,
            clientInput: [$key => $this->clientInput[$key] ?? []],
        );

        // try {
        //     $this->sortedResult = $sort->sort();
        // } catch (NoActionWillPerformException $e) {
        //     //
        // }

        return $this;
    }

    public function execute()
    {
        $run = [
            'projection' => function () {
                if ($key = Projection::getKeyCanUse()) {
                    if (empty($fields = $this->projection[$key]->project())) {
                        return [];
                    }

                    $this->builder->select($fields);
                }
            },

            'sort' => function (): void {
                $sortedResult = $this->sort->sort();

                foreach ($sortedResult as $field => $direction) {
                    $this->builder->orderBy($field, $direction);
                }
            },
        ];

        try {
            foreach ($run as $feature => $cb) {
                if (! is_null($this->{$feature})) {
                    $cb();
                }
            }
        } catch (DisabledException $e) {
            // noop
        }

        // if ($this->searchWasExecute()) {
        //     $searchFromFields = filter_explode(key($this->searchedResult));
        //     $searchKeyword = current($this->searchedResult);

        //     $this->builder->where(fn ($builderInner) => $builderInner->whereAny($searchFromFields, 'LIKE', $searchKeyword));
        // }

        return $this->builder->get();
    }
}
