<?php

namespace Tuner\Requests;

use Illuminate\Database\Eloquent\Model;
use Tuner\Columns\ExpandableRelations;
use Tuner\Tuner;

/**
 * @internal
 */
class ExpansionRequest extends Request implements RequestInterface
{
    // const KEY_LIMIT = 'limit';

    // const KEY_OFFSET = 'offset';

    public function __construct(
        array $config,
        array $request,
        private Model $subjectModel,
        private array $visibleColumns,
        private array $expandableRelations,
    ) {
        parent::__construct($config[Tuner::PARAM_KEY], $request);
    }

    protected function validate()
    {
        $expandableRelations = (new ExpandableRelations($this->subjectModel, $this->expandableRelations, $this->visibleColumns))();

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
