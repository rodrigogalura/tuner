<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laradigs\Tweaker\CanTweak;

class NoProjectableModel extends Model
{
    use CanTweak;

    /** @use HasFactory<\Database\Factories\NoProjectableModelFactory> */
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
        return [];
    }

    protected function getSearchableFields()
    {
        return [];
    }
}
