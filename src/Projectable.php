<?php

namespace RGalura\ApiIgniter;

use function RGalura\ApiIgniter\filter_explode;

trait Projectable
{
    private static function fields(array|string $projectable, $client_fields_key = 'fields', $client_excepts_key = 'fields!')
    {
        if (empty($projectable['fields'])) {
            return [];
        }

        if (is_string($projectable['fields'])) {
            $projectable['fields'] = filter_explode($projectable['fields']);
        }

        $clientExceptFields = filter_explode($_GET[$client_excepts_key] ?? '');

        $clientFields = match (true) {
            empty($clientExceptFields) => filter_explode($_GET[$client_fields_key] ?? '*'),
            empty($_GET[$client_fields_key]) => array_diff(
                $projectable['columnListing'],
                $clientExceptFields
            ),
            default => array_diff(
                filter_explode($_GET[$client_fields_key]),
                $clientExceptFields
            )
        };

        return match (true) {
            $projectable['fields'] === ['*'] => $clientFields,
            $clientFields === ['*'] => $projectable['fields'],
            default => array_intersect($projectable['fields'], $clientFields),
        };
    }
}
