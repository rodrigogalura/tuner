<?php

namespace Tuner\Fields;

use Tuner\Exceptions\TunerException;

use function Tuner\any;

/**
 * @internal
 */
class DefinedFields extends Fields
{
    const ERR_CODE_QUERY_EXCEPTION = 3;

    const ERR_MSG_QUERY_EXCEPTION = 'Defined fields are empty!';

    const ERR_CODE_DCOLS_VCOLS_NO_MATCH = 4;

    const ERR_MSG_DCOLS_VCOLS_NO_MATCH = 'Defined fields are invalid. It must be at least one match in visible fields!';

    public function __construct(array $fields, array $visibleFields)
    {
        parent::__construct($fields, $visibleFields);

        $this->validate();
    }

    private function validate()
    {
        throw_if(empty($this->fields), new TunerException(static::ERR_MSG_QUERY_EXCEPTION, static::ERR_CODE_QUERY_EXCEPTION));

        throw_unless(any(parent::__invoke(), $this->visibleFields), new TunerException(static::ERR_MSG_DCOLS_VCOLS_NO_MATCH, static::ERR_CODE_DCOLS_VCOLS_NO_MATCH));
    }
}
