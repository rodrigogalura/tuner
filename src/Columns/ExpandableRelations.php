<?php

namespace Tuner\Columns;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Model;
use Tuner\Exceptions\TunerException;

enum ExpansionOptions: string
{
    case Projectable = 'projectable_columns';
    case Sortable = 'sortable_columns';
    case Searchable = 'searchable_columns';
    case Filterable = 'filterable_columns';
}

/**
 * @internal
 */
// class ExpandableRelations extends Columns
class ExpandableRelations
{
    const ERR_CODE_DISABLED = 11;

    const ERR_MSG_DISABLED = 'Expandable relations are empty!';

    const ERR_CODE_INVALID_RELATION = 12;

    const ERR_MSG_INVALID_RELATION = 'Model [%s] has no available relation [%s]!';

    const ERR_CODE_INVALID_OPTION = 13;

    const ERR_MSG_INVALID_OPTION = 'Expansion option [%s] is invalid!';

    // const ERR_CODE_PCOLS_VCOLS_NO_MATCH = 2;

    // const ERR_MSG_PCOLS_VCOLS_NO_MATCH = 'Expandable relations are invalid. It must be at least one match in visible columns!';

    public function __construct(private Model $subjectModel, private array $expandableRelations)
    {
        $this->validate();
    }

    private function validate()
    {
        $modelName = class_basename($this->subjectModel::class);

        foreach ($this->expandableRelations as $relation => $settings) {
            try {
                $relationModel = $this->subjectModel->{$relation}();
            } catch (BadMethodCallException $e) {
                throw new TunerException(sprintf(static::ERR_MSG_INVALID_RELATION, $modelName, $relation), static::ERR_CODE_INVALID_RELATION);
            }

            foreach ($settings['options'] as $option => $columns) {
                $eOption = ExpansionOptions::tryFrom($option);
                throw_if(is_null($eOption), new TunerException(sprintf(static::ERR_MSG_INVALID_OPTION, $option), static::ERR_CODE_INVALID_OPTION));
            }
        }
    }
}
