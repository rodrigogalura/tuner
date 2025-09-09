<?php

namespace RodrigoGalura\Tuner\V33\ValueObjects\Requests;

use Exception;
use RodrigoGalura\Tuner\V33\Tuner;

class LimitRequest extends Request
{
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
        // Validate limit
        $limitRequest = current($this->request); // unwrap
        throw_unless(is_numeric($limitRequest), new Exception('The ['.$this->key.'] must be numeric!'));
    }
}
