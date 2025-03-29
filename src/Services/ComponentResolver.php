<?php

namespace RGalura\ApiIgniter\Services;

use Illuminate\Support\Arr;

class ComponentResolver
{
    public static array $components = [];

    public static function bind($key, callable $component)
    {
        self::$components[$key] = $component;
    }

    public static function resolve($key)
    {
        $component = Arr::pull(self::$components, $key);

        return $component();
    }
}
