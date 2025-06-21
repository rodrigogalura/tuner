<?php

namespace RGalura\ApiIgniter;

use function RGalura\ApiIgniter\filter_explode;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;
use RGalura\ApiIgniter\Exceptions\ImproperUsedProjectionException;
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

        switch (true) {
            case isset($fields, $exclude):
                throw new ImproperUsedProjectionException($clientFieldsKey, $clientExcludeFieldsKey);

            case !isset($fields) && !isset($exclude):
                return $projectableFields;

            case isset($fields):
                return $this->includeFields($columnListing, $projectableFields, filter_explode($fields ?? ''));

            case isset($exclude):
                return $this->excludeFields($columnListing, $projectableFields, filter_explode($exclude ?? ''));
        }
    }

    private function includeFields(array $columnListing, array $projectableFields, array $fields)
    {
        return match (true) {
            $projectableFields === ['*'] => $fields,
            $fields === ['*'] => $projectableFields,
            ! empty(array_diff($fields, $projectableFields)) => throw new InvalidFieldsException(array_values($diff)),
            default => array_intersect($projectableFields, $fields)
        };
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
