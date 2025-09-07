<?php

namespace RodrigoGalura\Tuner\V33\ValueObjects\Requests;

abstract class SingleKeyRequest extends Request implements RequestInterface
{
    public function __construct(
        protected string $singleKey,
        protected array $visibleColumns,
        array $request
    ) {
        parent::__construct($singleKey, $request);
    }

    protected function beforeValidate()
    {
        $this->request = array_filter($this->request, fn ($paramKey): bool => $paramKey === $this->singleKey, ARRAY_FILTER_USE_KEY);
    }
}
