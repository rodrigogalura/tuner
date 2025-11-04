<?php

namespace Tuner\Requests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Schema;
use Tuner\Columns\ExpandableRelations;
use Tuner\Exceptions\ClientException;
use Tuner\Exceptions\TunerException;
use Tuner\Tuner;

/**
 * @internal
 */
class ExpansionRequest extends Request implements RequestInterface
{
    public function __construct(
        array $request,
        private array $config,
        private Model $subjectModel,
        private array $definedColumns,
        private array $expandableRelations,
    ) {
        parent::__construct($request);
    }

    private function appendExpandableRelations(string $relation, array $data)
    {
        foreach ($data as $key => $value) {
            $this->expandableRelations[$relation][$key] = $value;
        }
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
        $expandableRelation = new ExpandableRelations($this->subjectModel, $this->expandableRelations);
        $relationBuilder = $expandableRelation->getRelationBuilder();

        $expansionConfig = $this->config[Tuner::CONFIG_EXPANSION];
        $expandKey = $expansionConfig[Tuner::PARAM_KEY];

        try {
            foreach ($this->request[$expandKey] as $relation => $alias) {

                if ($settings = $this->expandableRelations[$relation] ?? null) {
                    if (! isset($settings['table'])) {
                        $settings['table'] = $this->expandableRelations[$relation]['table'] = str($relation)->snake()->plural()->value;
                    }

                    $options = $settings['options'];
                    $columnListing = Schema::getColumnListing($settings['table']);

                    $features = [
                        implode(',', $this->config[Tuner::CONFIG_PROJECTION][Tuner::PARAM_KEY]) => fn ($projectionRequest): ProjectionRequest => new ProjectionRequest($projectionRequest, $this->config[Tuner::CONFIG_PROJECTION], $columnListing, $options['projectable_columns'], $this->definedColumns),
                        $this->config[Tuner::CONFIG_SORT][Tuner::PARAM_KEY] => fn ($sortRequest): SortRequest => new SortRequest($sortRequest, $this->config[Tuner::CONFIG_SORT], $columnListing, $options['sortable_columns']),
                        $this->config[Tuner::CONFIG_SEARCH][Tuner::PARAM_KEY] => fn ($searchRequest): SearchRequest => new SearchRequest($searchRequest, $this->config[Tuner::CONFIG_SEARCH], $columnListing, $options['searchable_columns']),
                        implode(',', $this->config[Tuner::CONFIG_FILTER][Tuner::PARAM_KEY]) => fn ($filterRequest): FilterRequest => new FilterRequest($filterRequest, $this->config[Tuner::CONFIG_FILTER], $columnListing, $options['filterable_columns']),
                    ];

                    foreach ($features as $key => $feature) {
                        $modifiers = explode(',', $key);

                        foreach ($modifiers as $modifier) {
                            $aliasKey = $alias.$expansionConfig['separator'].$modifier;

                            $request = [];
                            if ($requestValue = $this->request[$aliasKey] ?? null) {
                                $request[$modifier] = $requestValue;

                                $filteredRequest = $feature($request)();

                                $this->request[$aliasKey] = $filteredRequest[$modifier];
                            }
                        }
                    }

                    $this->appendExpandableRelations($relation, [
                        'fk' => $relationBuilder[$relation]->getForeignKeyName(),
                        'relationClass' => $relationBuilder[$relation]::class,
                    ]);
                }
            }
        } catch (TunerException|ClientException $e) {
            $class = get_class($e);
            throw new $class("Expansion [{$relation}]: ".$e->getMessage(), $e->getCode());
        }
    }

    public function getExpandableRelations()
    {
        return $this->expandableRelations;
    }
}
