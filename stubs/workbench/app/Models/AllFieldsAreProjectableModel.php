<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laradigs\Tweaker\CanTweak;

class AllFieldsAreProjectableModel extends Model
{
    use CanTweak;

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
}
