<?php

namespace RodrigoGalura\Tuner\V33\ValueObjects\Requests;

use Exception;
use RodrigoGalura\Tuner\V33\Tuner;

class PaginationRequest extends Request implements RequestInterface
{
    public function __construct(
        array $config,
        array $request,
        private bool $paginatable,
    ) {
        parent::__construct($config[Tuner::PARAM_KEY], $request);
    }

    protected function shouldValidate()
    {
        return $this->paginatable;
    }

    protected function validate()
    {
        $pageSize = current($this->request);

        // Validate pageSize
        throw_unless(is_numeric($pageSize), new Exception('The ['.$this->key.'] must be numeric!', 422));
    }
}
