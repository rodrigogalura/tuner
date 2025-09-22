<?php

namespace Tuner\Requests;

use Schema;
use Tuner\Tuner;
use Illuminate\Support\Str;
use Tuner\Exceptions\TunerException;
use Tuner\Exceptions\ClientException;
use Tuner\Columns\ExpandableRelations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BuilderContract;

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

    private function shouldAddForeignKey(Builder $relation)
    {
        return !is_a($relation, BelongsTo::class);
    }

    private function setExpandableRelations(string $relation, $fk)
    {
        return $this->expandableRelations[$relation]['fk'] = $fk;
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
        new ExpandableRelations($this->subjectModel, $this->expandableRelations);

        $expansionConfig = $this->config[Tuner::CONFIG_EXPANSION];

        $expandKey = $expansionConfig[Tuner::PARAM_KEY];

        try {
            foreach ($this->request[$expandKey] as $relation => $alias) {

                if ($settings = $this->expandableRelations[$relation] ?? null) {
                    if (! isset($settings['table'])) {
                        $settings['table'] = $this->expandableRelations[$relation]['table'] = Str::plural($relation);
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

                    if ($this->shouldAddForeignKey($this->subjectModel->{$relation}())) {
                        when(! isset($settings['fk']), fn () => $this->setExpandableRelations($relation, $this->subjectModel->getForeignKey()));
                    }
                }
            }
        } catch (TunerException|ClientException $e) {
            $class = get_class($e);
            throw new $class("Expansion [{$relation}]: ".$e->getMessage());
        }
    }

    public function getExpandableRelations()
    {
        return $this->expandableRelations;
    }
}
