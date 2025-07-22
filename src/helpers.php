<?php

namespace RGalura\ApiIgniter;

function filter_explode(string $string, string $delimiter = ',')
{
    return array_filter(array_map('trim', explode($delimiter, $string)));
}

function is_multi_array(array $arr)
{
    while ($current = current($arr)) {
        if (is_array($current)) {
            return true;
        }

        next($arr);
    }

    return false;
}
