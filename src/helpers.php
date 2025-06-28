<?php

namespace RGalura\ApiIgniter;

function filter_explode(string $string, string $delimiter = ',')
{
    return array_filter(array_map('trim', explode($delimiter, $string)));
}

function array_insert(&$array, $position, $item)
{
    if ($position === -1) {
        $position = count($array);
    }
    array_splice($array, $position, 0, $item);
}

function array_insert_multiple(&$arrayFrom, $arrayTo)
{
    while ($item = current($arrayTo)) {
        array_insert($arrayFrom, key($arrayTo), $item);

        next($arrayTo);
    }
}
