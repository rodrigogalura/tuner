<?php

namespace Tuner;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @internal
 */
class Tuner
{
    const PARAM_KEY = 'key';

    const CONFIG_PROJECTION = 'projection';

    const CONFIG_SORT = 'sort';

    const CONFIG_SEARCH = 'search';

    const CONFIG_FILTER = 'filter';

    const CONFIG_EXPANSION = 'expansion';

    const CONFIG_LIMIT = 'limit';

    const CONFIG_PAGINATION = 'pagination';

    private TunerBuilder $builder;

    public array $visibleFields;

    public function __construct(Builder $builder, array $request, Model $model)
    {
        $this->builder = TunerBuilder::create($builder, $request);

        $this->visibleFields = array_diff(
            $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable()),
            $model->getHidden()
        );
    }

    public function getBuilder()
    {
        return $this->builder;
    }
}
