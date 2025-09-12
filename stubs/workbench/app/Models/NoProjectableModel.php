<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoProjectableModel extends Model
{
    /** @use HasFactory<\Database\Factories\NoProjectableModelFactory> */
    use HasFactory;

    use \Tuner\V33\Tunable;

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected function getProjectableFields()
    {
        return [];
    }

    protected function getSearchableFields()
    {
        return [];
    }

    protected function getSortableFields()
    {
        return [];
    }
}
