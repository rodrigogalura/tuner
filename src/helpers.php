<?php

namespace RGalura\ApiIgniter;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

function filter_explode(string $string, string $delimiter = ',')
{
    return array_filter(array_map('trim', explode($delimiter, $string)));
}

function is_multi_array(array $arr): bool
{
    while ($current = current($arr)) {
        if (is_array($current)) {
            return true;
        }

        next($arr);
    }

    return false;
}

function http_response_error($message, $errors = [])
{
    $success = false;

    return empty($errors)
        ? compact('success', 'message')
        : compact('success', 'message', 'errors');
}

function validate($input, $rule, $customErrorMessage = null): void
{
    $keys = array_keys($input);

    $validator = Validator::make($input,
        array_fill_keys($keys, $rule),
        is_null($customErrorMessage) ? [] : array_fill_keys($keys, $customErrorMessage),
    );

    if ($validator->fails()) {
        throw new ValidationException($validator, 422);
    }
}

function validate2($input, $rule, $customErrorMessages = []): void
{
    $validator = Validator::make($input, $rule, $customErrorMessages);

    if ($validator->fails()) {
        throw new ValidationException($validator, 422);
    }
}

function base_path(string $subPath = '')
{
    return realpath(__DIR__.'/..').($subPath ? DIRECTORY_SEPARATOR.$subPath : '');
}

function assign_if($compareTo, &$var, $newValue, bool $strict = true): void
{
    if ($strict
        ? $var === $compareTo
        : $var == $compareTo
    ) {
        $var = $newValue;
    }
}

function in_array_all($needles, $haystack)
{
    return empty(array_diff($haystack, $needles));
}
