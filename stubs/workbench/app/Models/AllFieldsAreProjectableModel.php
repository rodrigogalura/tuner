<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tuner\Tunable;

class AllFieldsAreProjectableModel extends Model
{
    use \Tuner\V33\Tunable;

    /** @use HasFactory<\Database\Factories\AllFieldsAreProjectableModelFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected function getProjectableFields()
    {
        return ['id', 'name'];
    }

    protected function getSearchableFields()
    {
        return ['id', 'name'];
    }

    protected function getSortableFields()
    {
        return ['id', 'name'];
    }
}
