<?php

namespace Laradigs\Tweaker;

use RGalura\ApiIgniter\filter_explode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Laradigs\Tweaker\Searching\Searching;
use function RGalura\ApiIgniter\filter_explode;
use Laradigs\Tweaker\Projection\ProjectionField;
use Laradigs\Tweaker\Projection\ProjectionFieldNot;
use Laradigs\Tweaker\Projection\NoActionWillPerformException;

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
    )
    {
        //
    }

    /**
     * Singletons should not be cloneable.
     */
    protected function __clone() { }

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    private function projectionWasExecute()
    {
        return !is_null($this->projectedFields);
    }

    private function searchingWasExecute()
    {
        return !is_null($this->searchedResult);
    }

    public static function getInstance(
        Builder $builder,
        Model $model,
        array $config,
        array $clientInput
    )
    {
        return new TweakerBuilder(
            builder: $builder,
            model: $model,
            config: $config,
            clientInput: $clientInput
        );
    }

    public function projection($projectableFields)
    {
        $clientInputField = $this->clientInput[$this->config['projection']['include_key']] ?? null;
        $clientInputFieldNot = $this->clientInput[$this->config['projection']['exclude_key']] ?? null;

        if (isset($clientInputField) xor isset($clientInputFieldNot)) {
            $projection = match (true) {
                ! is_null($clientInputField) => new ProjectionField(
                    model: $this->model,
                    projectableFields: $projectableFields,
                    definedFields: $this->builder->getQuery()->columns ?? ['*'],
                    clientInput: filter_explode($clientInputField),
                ),
                ! is_null($clientInputFieldNot) => new ProjectionFieldNot(
                    model: $this->model,
                    projectableFields: $projectableFields,
                    definedFields: $this->builder->getQuery()->columns ?? ['*'],
                    clientInput: filter_explode($clientInputFieldNot),
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

    public function searchFilter($searchableFields)
    {
        $clientInputSearch = $_GET[$this->config['searching']['key']] ?? null;

        if (isset($clientInputSearch)) {
            $searching = new Searching(
                model: $this->model,
                searchableFields: $searchableFields,
                clientInput: $clientInputSearch,
            );

            try {
                $this->searchedResult = $searching->search();

                // $builder->whereAny(filter_explode(key($searchResult)), 'LIKE', current($searchResult));
            } catch (NoActionWillPerformException $e) {
                //
            }
        }

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

        if ($this->searchingWasExecute()) {
            if (empty($this->searchedResult)) {
                return [];
            }

            $searchFromFields = filter_explode(key($this->searchedResult));
            $searchKeyword = current($this->searchedResult);

            $this->builder->where(fn ($builderInner) => $builderInner->whereAny($searchFromFields, 'LIKE', $searchKeyword));
        }

        return $this->builder->get();
    }
}
