<?php

namespace Tuner\Requests;

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
        // private Model $subjectModel,
        // private array $visibleColumns,
        // private array $expandableRelations,
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
        dd($this->request);
    }

    protected function validate()
    {
        // $expandableRelations = (new ExpandableRelations($this->subjectModel, $this->expandableRelations, $this->visibleColumns))();

        // $limitRequest = $this->request;

        // // Validate limit
        // throw_unless($limit = $limitRequest[static::KEY_LIMIT] ?? null, new ClientException('The ['.static::KEY_LIMIT.'] is required!'));
        // throw_unless(is_numeric($limit), new ClientException('The ['.static::KEY_LIMIT.'] must be numeric!'));

        // if ($offset = $limitRequest[static::KEY_OFFSET] ?? null) {
        //     // Validate offset
        //     throw_unless(is_numeric($offset), new ClientException('The ['.static::KEY_OFFSET.'] must be numeric!'));
        // }
    }
}
