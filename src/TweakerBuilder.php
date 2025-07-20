<?php

namespace Laradigs\Tweaker;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;
use Laradigs\Tweaker\Projection\ProjectionField;
use Laradigs\Tweaker\Projection\ProjectionFieldNot;
use Laradigs\Tweaker\Search\Search;
use RGalura\ApiIgniter\filter_explode;

use function RGalura\ApiIgniter\filter_explode;

/**
 * Singleton
 */
final class TweakerBuilder
{
    private ?array $projectedFields = null;

    private ?array $searchedResult = null;

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    private function __construct(
        private Builder $builder,
        private Model $model,
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
        Model $model,
        array $config,
        array $clientInput
    ) {
        // return new static(
        return new TweakerBuilder(
            builder: $builder,
            model: $model,
            config: $config,
            clientInput: $clientInput
        );
    }

    public function projection(array $projectableFields)
    {
        $clientInputField = $this->clientInput[$this->config['projection']['include_key']] ?? null;
        $clientInputFieldNot = $this->clientInput[$this->config['projection']['exclude_key']] ?? null;

        if (isset($clientInputField) xor isset($clientInputFieldNot)) {
            $projection = match (true) {
                ! is_null($clientInputField) => new ProjectionField(
                    model: $this->model,
                    projectableFields: $projectableFields,
                    definedFields: $this->builder->getQuery()->columns ?? ['*'],
                    clientInput: $clientInputField,
                ),
                ! is_null($clientInputFieldNot) => new ProjectionFieldNot(
                    model: $this->model,
                    projectableFields: $projectableFields,
                    definedFields: $this->builder->getQuery()->columns ?? ['*'],
                    clientInput: $clientInputFieldNot,
                ),
            };

            try {
                $this->projectedFields = $projection->project();
            } catch (NoActionWillPerformException $e) {
                //
            }
        }

        return $this;
    }

    public function searchFilter(array $searchableFields)
    {
        $clientInputSearch = $this->clientInput[$this->config['search']['key']] ?? null;

        if (isset($clientInputSearch)) {
            $search = new Search(
                model: $this->model,
                searchableFields: $searchableFields,
                clientInput: $clientInputSearch,
            );

            try {
                $this->searchedResult = $search->search();
            } catch (NoActionWillPerformException $e) {
                //
            }
        }

        return $this;
    }

    public function sort(array $sortable)
    {
        $clientInputSort = $this->clientInput[$this->config['sort']['key']] ?? null;

        // if (isset($clientInputSort)) {
        //     $search = new Search(
        //         model: $this->model,
        //         searchableFields: $searchableFields,
        //         clientInput: $clientInputSort,
        //     );

        //     try {
        //         $this->searchedResult = $search->search();

        //         // $builder->whereAny(filter_explode(key($searchResult)), 'LIKE', current($searchResult));
        //     } catch (NoActionWillPerformException $e) {
        //         //
        //     }
        // }
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
