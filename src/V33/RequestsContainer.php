<?php

namespace RodrigoGalura\Tuner\V33;

use Exception;

class RequestsContainer
{
    private array $requests = [];

    public function bind(string $key, callable $factory): void
    {
        $this->requests[$key] = $factory;
    }

    private function resolve(string $key)
    {
        if (! isset($this->requests[$key])) {
            throw new Exception("Target request [{$key}] does not exist.");
        }

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
