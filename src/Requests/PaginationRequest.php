<?php

namespace Tuner\Requests;

use Tuner\Tuner;

/**
 * @internal
 */
class PaginationRequest extends Request implements RequestInterface
{
    public function __construct(
        array $config,
        array $request,
        private bool $paginatable,
    ) {
        parent::__construct($request, $config[Tuner::PARAM_KEY]);
    }

    protected function shouldValidate()
    {
        return $this->paginatable;
    }

    protected function validate()
    {
        $pageSize = current($this->request);

        // Validate pageSize
        throw_unless(is_numeric($pageSize), new ClientException('The ['.$this->key.'] must be numeric!'));
    }
}
