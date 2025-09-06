<?php

namespace Laradigs\Tweaker\V33\ValueObjects\Requests;

abstract class Request implements RequestInterface
{
    public function __construct(
        protected array|string $key,
        protected array $request
    ) {
        $this->beforeValidate();
        $this->validate();
    }

    public function __invoke(): array
    {
        return $this->request;
    }

    protected function beforeValidate()
    {
        //
    }

    abstract protected function validate();
}
