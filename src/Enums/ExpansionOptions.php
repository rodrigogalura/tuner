<?php

namespace Tuner\Enums;

enum ExpansionOptions: string
{
    case Projectable = 'projectable_fields';
    case Sortable = 'sortable_fields';
    case Searchable = 'searchable_fields';
    case Filterable = 'filterable_fields';
}
