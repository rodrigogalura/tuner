<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllFieldsAreProjectableModel extends Model
{
    /** @use HasFactory<\Database\Factories\AllFieldsAreProjectableModelFactory> */
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
