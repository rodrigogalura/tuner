<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tuner\Tunable;

class NoProjectableModel extends Model
{
    /** @use HasFactory<\Database\Factories\NoProjectableModelFactory> */
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
        return [];
    }

    protected function getSearchableColumns()
    {
        return [];
    }

    protected function getSortableColumns()
    {
        return [];
    }
}
