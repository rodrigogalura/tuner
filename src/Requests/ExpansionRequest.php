<?php

namespace Tuner\Requests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Tuner\Columns\ExpandableRelations;
use Tuner\Exceptions\ClientException;
use Tuner\Exceptions\TunerException;
use Tuner\Tuner;

/**
 * @internal
 */
class ExpansionRequest extends Request implements RequestInterface
{
    // const KEY_LIMIT = 'limit';

    // const KEY_OFFSET = 'offset';

    public function __construct(
        array $request,
        private array $config,
        private Model $subjectModel,
        private Builder $builder,
        private array $visibleColumns,
        private array $expandableRelations,
    ) {
        parent::__construct($request);
    }

    protected function filterRequest()
    {
        $conditionFn = function ($paramKey): bool {
            $expansionConfig = $this->config[Tuner::CONFIG_EXPANSION];

            $expandKey = $expansionConfig[Tuner::PARAM_KEY];
            if (! array_key_exists($expandKey, $this->request)) {
                goto deny;
            }

            if ($expandKey === $paramKey) {
                return true;
            }

            foreach ($this->request[$expandKey] as $alias) {
                $validKeys = array_map(fn ($key): string => $alias.$expansionConfig['separator'].$key, [
                    ...$this->config[Tuner::CONFIG_PROJECTION][Tuner::PARAM_KEY],
                    $this->config[Tuner::CONFIG_SORT][Tuner::PARAM_KEY],
                    $this->config[Tuner::CONFIG_SEARCH][Tuner::PARAM_KEY],
                    ...$this->config[Tuner::CONFIG_FILTER][Tuner::PARAM_KEY],
                ]);

                if (in_array($paramKey, $validKeys)) {
                    return true;
                }
            }

            deny:
            return false;
        };

        $this->request = array_filter($this->request, fn ($paramKey): bool => $conditionFn($paramKey), ARRAY_FILTER_USE_KEY);
    }

    protected function validate()
    {
        new ExpandableRelations($this->subjectModel, $this->expandableRelations, $this->visibleColumns);

        $expansionConfig = $this->config[Tuner::CONFIG_EXPANSION];

        $expandKey = $expansionConfig[Tuner::PARAM_KEY];

        try {
            foreach ($this->request[$expandKey] as $relation => $alias) {
                $features = [
                    implode(',', $this->config[Tuner::CONFIG_PROJECTION][Tuner::PARAM_KEY]) => fn ($projectionRequest): ProjectionRequest => new ProjectionRequest($projectionRequest, $this->config[Tuner::CONFIG_PROJECTION], $this->visibleColumns, $this->expandableRelations[$relation]['projectable_columns'], $this->builder->getQuery()->columns ?? ['*']),
                    $this->config[Tuner::CONFIG_SORT][Tuner::PARAM_KEY] => fn ($sortRequest): SortRequest => new SortRequest($sortRequest, $this->config[Tuner::CONFIG_SORT], $this->visibleColumns, $this->expandableRelations[$relation]['sortable_columns']),
                    $this->config[Tuner::CONFIG_SEARCH][Tuner::PARAM_KEY] => fn ($searchRequest): SearchRequest => new SearchRequest($searchRequest, $this->config[Tuner::CONFIG_SEARCH], $this->visibleColumns, $this->expandableRelations[$relation]['searchable_columns']),
                    implode(',', $this->config[Tuner::CONFIG_FILTER][Tuner::PARAM_KEY]) => fn ($filterRequest): FilterRequest => new FilterRequest($filterRequest, $this->config[Tuner::CONFIG_FILTER], $this->visibleColumns, $this->expandableRelations[$relation]['filterable_columns']),
                ];

                foreach ($features as $key => $feature) {
                    $subKeys = explode(',', $key);

                    foreach ($subKeys as $subKey) {
                        $request = [];
                        if ($value = $this->request[$alias.$expansionConfig['separator'].$subKey] ?? null) {
                            $request[$subKey] = $value;
                        }

                        $feature($request);
                    }
                }
            }
        } catch (TunerException|ClientException $e) {
            $class = get_class($e);
            throw new $class("Expansion [{$relation}]: ".$e->getMessage());
        }

        exit('pass');
    }
}
