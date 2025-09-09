<?php

namespace RodrigoGalura\Tuner\V33\ValueObjects\Requests;

use Exception;
use RodrigoGalura\Tuner\V33\Tuner;

class LimitRequest extends Request
{
    const KEY_LIMIT = 'limit';

    const KEY_OFFSET = 'offset';

    public function __construct(
        array $config,
        array $request,
        private bool $limitable,
    ) {
        parent::__construct($config[Tuner::PARAM_KEY], $request);
    }

    protected function shouldValidate()
    {
        return $this->limitable;
    }

    protected function validate()
    {
        $limitRequest = $this->request;

        // Validate limit
        throw_unless($limit = $limitRequest[static::KEY_LIMIT] ?? null, new Exception('The ['.static::KEY_LIMIT.'] is required!', 422));
        throw_unless(is_numeric($limit), new Exception('The ['.static::KEY_LIMIT.'] must be numeric!', 422));

        if ($offset = $limitRequest[static::KEY_OFFSET] ?? null) {
            // Validate offset
            throw_unless(is_numeric($offset), new Exception('The ['.static::KEY_OFFSET.'] must be numeric!', 422));
        }
    }
}
