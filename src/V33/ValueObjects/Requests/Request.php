<?php

namespace Laradigs\Tweaker\V33\ValueObjects\Requests;

abstract class Request implements RequestInterface
{
    public function __construct(
        protected array $key,
        protected array $request
    ) {
        $this->validate();
    }

    public function __invoke(): array
    {
        return $this->request;
    }

    abstract protected function validate();
}
