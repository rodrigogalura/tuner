<?php

namespace RodrigoGalura\Tuner\V33\ValueObjects\Requests;

abstract class MultipleKeysRequest extends Request implements RequestInterface
{
    public function __construct(
        protected array $multipleKeys,
        protected array $visibleColumns,
        array $request
    ) {
        parent::__construct($multipleKeys, $request);
    }

    protected function beforeValidate()
    {
        $this->request = array_filter($this->request, fn ($paramKey): bool => in_array($paramKey, $this->multipleKeys), ARRAY_FILTER_USE_KEY);
    }
}
