<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvalidProjectableModel extends Model
{
    /** @use HasFactory<\Database\Factories\InvalidProjectableModelFactory> */
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
        return ['email'];
    }

    protected function getSearchableFields()
    {
        return ['email'];
    }

    protected function getSortableFields()
    {
        return ['email'];
    }
}
