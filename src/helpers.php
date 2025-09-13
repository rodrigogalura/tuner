<?php

/**
 * @internal
 */

namespace Tuner;

function explode_sanitize(string $string, string $delimiter = ',')
{
    return array_filter(array_map('trim', explode($delimiter, $string)));
}

/**
 * Check if any needle is in the haystack
 */
function any(array $needles, array $haystack): bool
{
    return ! empty(array_intersect($haystack, $needles));
}

function whenNotEmpty($subject, callable $callback)
{
    return when(! empty($subject), $callback);
}

function whenNotSet($subject, callable $callback)
{
    return when(! isset($subject), $callback);
}
