<?php

namespace Tuner\Fields;

use Tuner\Exceptions\TunerException;

use function Tuner\any;

/**
 * @internal
 */
class SearchableFields extends Fields
{
    const ERR_CODE_DISABLED = 7;

    const ERR_MSG_DISABLED = 'SearchableFields fields are empty!';

    const ERR_CODE_PCOLS_VCOLS_NO_MATCH = 8;

    const ERR_MSG_PCOLS_VCOLS_NO_MATCH = 'SearchableFields fields are invalid. It must be at least one match in visible fields!';

    public function __construct(array $fields, array $visibleFields)
    {
        parent::__construct($fields, $visibleFields);

        $this->validate();
    }

    private function validate()
    {
        throw_if(empty($this->fields), new TunerException(static::ERR_MSG_DISABLED, static::ERR_CODE_DISABLED));

        throw_unless(any(parent::__invoke(), $this->visibleFields), new TunerException(static::ERR_MSG_PCOLS_VCOLS_NO_MATCH, static::ERR_CODE_PCOLS_VCOLS_NO_MATCH));
    }
}
