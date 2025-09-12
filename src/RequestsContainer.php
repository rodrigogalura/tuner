<?php

namespace Tuner;

use Exception;

/**
 * @internal
 */
class RequestsContainer
{
    private array $requests = [];

    public function bind(string $key, callable $factory): void
    {
        $this->requests[$key] = $factory;
    }

    private function resolve(string $key)
    {
        when(! isset($this->requests[$key]), fn () => throw new Exception("Target request [{$key}] does not exist."));

        $factory = $this->requests[$key];

        return $factory($this);
    }

    public function resolveAndRunCallbackWhenRequested(string $key, callable $callback)
    {
        $request = $this->resolve($key);

        when(! empty($request()), fn () => $callback($request));
    }

    public static function create()
    {
        return new static;
    }
}
