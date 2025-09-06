<?php

namespace Laradigs\Tweaker\V33\ValueObjects\Requests;

abstract class SingleKeyRequest extends Request
{
    protected function beforeValidate()
    {
        $this->request = array_filter($this->request, fn ($paramKey): bool => $paramKey === $this->key, ARRAY_FILTER_USE_KEY);
    }
}
