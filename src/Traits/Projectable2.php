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
            return [];
        }

        $fields = $_GET[$clientFieldsKey] ?? null;
        $exclude = $_GET[$clientExcludeFieldsKey] ?? null;

        if ($projectableFields === ['*']) {
            // column listing as projectableFields
            $projectableFields = array_diff(
                $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable()),
                $this->getHidden()
            );
        }

        $includeFn = function (array $projectableFields, array $fields) {
            switch (true) {
                case $fields === ['*']:
                    return $projectableFields;

                case ! empty($diff = array_diff($fields, $projectableFields)):
                    throw new InvalidFieldsException(array_values($diff));
                default:
                    return array_values(array_intersect($projectableFields, $fields));
            }
        };

        $excludeFn = function (array $projectableFields, array $exclude) {
            return match (true) {
                $exclude === ['*'] => throw new ExcludeFieldsException($exclude),

                default => array_values(array_diff($projectableFields, $exclude))
            };
        };

        return match (true) {
            isset($fields, $exclude) => throw new ImproperUsedProjectionException($clientFieldsKey, $clientExcludeFieldsKey),
            ! isset($fields) && ! isset($exclude) => $projectableFields,
            isset($fields) => $includeFn($projectableFields, filter_explode($fields ?? '')),
            isset($exclude) => $excludeFn($projectableFields, filter_explode($exclude ?? '')),
        };
    }
}
