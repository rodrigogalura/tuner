<?php

namespace RGalura\ApiIgniter;

use RGalura\ApiIgniter\Exceptions\ExcludeFieldsException;
use RGalura\ApiIgniter\Exceptions\ImproperUsedProjectionException;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;

trait Projectable2
{
    private function projectedFields(array $projectableFields, $clientFieldsKey = 'fields', $clientExcludeFieldsKey = 'fields!')
    {
        if (empty($projectableFields)) {
            return null;
        }

        $includeFn = function (array $projectableFields, array $include) {
            return match (true) {
                $include === ['*'] => $projectableFields,
                ! empty($diff = array_diff($include, $projectableFields)) => throw new InvalidFieldsException(array_values($diff)),
                default => array_intersect($projectableFields, $include)
            };
        };

        $excludeFn = function (array $projectableFields, array $exclude) {
            return match (true) {
                $exclude === ['*'] => throw new ExcludeFieldsException($exclude),
                ! empty($diff = array_diff($exclude, $projectableFields)) => throw new InvalidFieldsException(array_values($diff)),
                default => array_diff($projectableFields, $exclude)
            };
        };

        $include = $_GET[$clientFieldsKey] ?? null;
        $exclude = $_GET[$clientExcludeFieldsKey] ?? null;

        return match (true) {
            isset($include) && isset($exclude) => throw new ImproperUsedProjectionException($clientFieldsKey, $clientExcludeFieldsKey),
            isset($include) => $includeFn($projectableFields, filter_explode($include ?? '')),
            isset($exclude) => $excludeFn($projectableFields, filter_explode($exclude ?? '')),
            default => null
        };
    }
}
