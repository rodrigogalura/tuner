<?php

namespace RGalura\ApiIgniter;

use RGalura\ApiIgniter\Exceptions\ImproperUsedProjectionException;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Exceptions\ProjectionExcludeFieldsException;

trait Projectable2
{
    private function projectedFields(array $projectableFields, $clientFieldsKey = 'fields', $clientExcludeFieldsKey = 'fields!')
    {
        if (empty($projectableFields)) {
            return [];
        }

        $fields = $_GET[$clientFieldsKey] ?? null;
        $exclude = $_GET[$clientExcludeFieldsKey] ?? null;

        $columnListing = array_diff(
            $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable()),
            $this->getHidden()
        );

        return match (true) {
            isset($fields, $exclude) => throw new ImproperUsedProjectionException($clientFieldsKey, $clientExcludeFieldsKey),
            ! isset($fields) && ! isset($exclude) => $projectableFields,
            isset($fields) => $this->includeFields($columnListing, $projectableFields, filter_explode($fields ?? '')),
            isset($exclude) => $this->excludeFields($columnListing, $projectableFields, filter_explode($exclude ?? '')),
        };
    }

    private function includeFields(array $columnListing, array $projectableFields, array $fields)
    {
        switch (true) {
            case $projectableFields === ['*']:
                if (! empty($diff = array_diff($fields, $columnListing))) {
                    throw new InvalidFieldsException(array_values($diff));
                }

                return $fields;

            case $fields === ['*']:
                return $projectableFields;

            case ! empty($diff = array_diff($fields, $projectableFields)):
                throw new InvalidFieldsException(array_values($diff));

            default:
                return array_intersect($projectableFields, $fields);
        }
    }

    private function excludeFields(array $columnListing, array $projectableFields, array $exclude)
    {
        return match (true) {
            $exclude === ['*'] => throw new ProjectionExcludeFieldsException($exclude),
            default => array_diff(
                $columnListing,
                $exclude
            )
        };
    }
}
