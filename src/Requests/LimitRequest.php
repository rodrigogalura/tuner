<?php

namespace Tuner\Requests;

use Tuner\Exceptions\ClientException;
use Tuner\Tuner;

/**
 * @internal
 */
class LimitRequest extends Request implements RequestInterface
{
    const KEY_LIMIT = 'limit';

    const KEY_OFFSET = 'offset';

    public function __construct(
        array $request,
        array $config,
        private bool $limitable,
    ) {
        parent::__construct($request, $config[Tuner::PARAM_KEY]);
    }

    protected function shouldValidate()
    {
        return $this->limitable;
    }

    protected function validate()
    {
        $limitRequest = $this->request;

        // Validate limit
        throw_unless($limit = $limitRequest[static::KEY_LIMIT] ?? null, new ClientException('The ['.static::KEY_LIMIT.'] is required!'));
        throw_unless(is_numeric($limit), new ClientException('The ['.static::KEY_LIMIT.'] must be numeric!'));

        if ($offset = $limitRequest[static::KEY_OFFSET] ?? null) {
            // Validate offset
            throw_unless(is_numeric($offset), new ClientException('The ['.static::KEY_OFFSET.'] must be numeric!'));
        }
    }
}
