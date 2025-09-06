<?php

namespace Laradigs\Tweaker\V33\ValueObjects\Requests;

abstract class MultipleKeysRequest extends Request
{
    public function __construct(
        protected array $keys,
        array $request
    ) {
        parent::__construct($keys, $request);
    }

    protected function beforeValidate()
    {
        $this->request = array_filter($this->request, fn ($paramKey): bool => in_array($paramKey, $this->keys), ARRAY_FILTER_USE_KEY);
    }
}
