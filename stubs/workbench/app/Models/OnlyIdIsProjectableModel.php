<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tuner\Tunable;

class OnlyIdIsProjectableModel extends Model
{
    use \Tuner\V33\Tunable;

    /** @use HasFactory<\Database\Factories\OnlyIdIsProjectableModelFactory> */
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
        return ['id'];
    }

    protected function getSearchableFields()
    {
        return ['id'];
    }

    protected function getSortableFields()
    {
        return ['id'];
    }
}
