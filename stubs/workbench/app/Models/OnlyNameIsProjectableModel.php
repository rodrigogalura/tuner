<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tuner\Tunable;

class OnlyNameIsProjectableModel extends Model
{
    /** @use HasFactory<\Database\Factories\OnlyNameIsProjectableModelFactory> */
    use HasFactory;

    use Tunable;

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected function getProjectableColumns()
    {
        return ['name'];
    }

    protected function getSearchableColumns()
    {
        return ['name'];
    }

    protected function getSortableColumns()
    {
        return ['name'];
    }
}
