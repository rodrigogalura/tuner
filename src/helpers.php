<?php

namespace RGalura\ApiIgniter;

function filter_explode(string $string, string $delimiter = ',')
{
    return array_filter(array_map('trim', explode($delimiter, $string)));
}
