<?php

namespace Workbench\App\Repositories;

use Workbench\App\Models\User;
use RGalura\ApiIgniter\Contracts\ApiIgniterInterface;

class UserRepository implements ApiIgniterInterface
{
    public function send()
    {
        return User::send(
            debuggable: true,
            paginatable: true,
            expandable: [
                'posts' => [
                    'projectable' => ['fields' => '*'],
                    'filterable_fields' => ['*'],
                    'searchable_fields' => ['*'],
                    'sortable_fields' => ['*'],
                ],
                'siblings' => [
                    'projectable' => ['fields' => '*'],
                    'filterable_fields' => ['*'],
                    'searchable_fields' => ['*'],
                    'sortable_fields' => ['*'],
                ],
            ]
        );
    }
}
