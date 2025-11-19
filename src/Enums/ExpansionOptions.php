<?php

namespace Tuner\Enums;

enum ExpansionOptions: string
{
    case Projectable = 'projectable_columns';
    case Sortable = 'sortable_columns';
    case Searchable = 'searchable_columns';
    case Filterable = 'filterable_columns';
}
