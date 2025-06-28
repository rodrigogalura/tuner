<?php

namespace RGalura\ApiIgniter;

// use RGalura\ApiIgniter\Exceptions\ExcludeFieldsException;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Exceptions\ImproperUsedProjectionException;
use RGalura\ApiIgniter\Exceptions\InvalidProjectableFieldsException;

trait Projectable2
{
    private function projectedFields(array $projectableFields, $clientFieldsKey = 'fields', $clientExcludeFieldsKey = 'fields!')
    {
        if (empty($projectableFields)) {
            return null;
        }

        if (! empty($diff = array_diff($projectableFields, $this->givenFields))) {
            throw new InvalidProjectableFieldsException(sprintf('\'%s\' cannot be set as projectable fields', implode(',', $diff)), 1);
        }

        $include = $_GET[$clientFieldsKey] ?? null;
        $exclude = $_GET[$clientExcludeFieldsKey] ?? null;

        if (is_null($include) && is_null($exclude)) {
            return null;
        }

        if (!is_null($include) && !is_null($exclude)) {
            throw new ImproperUsedProjectionException($clientFieldsKey, $clientExcludeFieldsKey);
        }

        $includeFn = function (array $projectableFields, array $include) {
            switch (true) {
                case in_array($include, [['*']]):
                    return $projectableFields;

                case ! empty($diff = array_diff($include, $projectableFields)):
                    throw new InvalidFieldsException(array_values($diff));

                default:
                    return array_intersect($projectableFields, $include);
            }
        };

        return match (true) {
            isset($include) => $includeFn($projectableFields, filter_explode($include ?? '')),
            isset($exclude) => $excludeFn($projectableFields, filter_explode($exclude ?? '')),
        };




        // if (empty($projectableFields)) {
        //     return [];
        // }

        // $fields = $_GET[$clientFieldsKey] ?? null;
        // $exclude = $_GET[$clientExcludeFieldsKey] ?? null;

        // if ($projectableFields === ['*']) {
        //     $projectableFields = $this->columnListing();
        // }

        // $includeFn = function (array $projectableFields, array $fields) {
        //     switch (true) {
        //         case $fields === ['*']:
        //             return $projectableFields;

        //         case ! empty($diff = array_diff($fields, $projectableFields)):
        //             throw new InvalidFieldsException(array_values($diff));

        //         default:
        //             return array_values(array_intersect($projectableFields, $fields));
        //     }
        // };

        // $excludeFn = function (array $projectableFields, array $exclude) {
        //     return match (true) {
        //         $exclude === ['*'] => throw new ExcludeFieldsException($exclude),

        //         default => array_values(array_diff($projectableFields, $exclude))
        //     };
        // };

        // return match (true) {
        //     isset($fields, $exclude) => throw new ImproperUsedProjectionException($clientFieldsKey, $clientExcludeFieldsKey),
        //     ! isset($fields) && ! isset($exclude) => $projectableFields,
        //     isset($fields) => $includeFn($projectableFields, filter_explode($fields ?? '')),
        //     isset($exclude) => $excludeFn($projectableFields, filter_explode($exclude ?? '')),
        // };
    }
}
