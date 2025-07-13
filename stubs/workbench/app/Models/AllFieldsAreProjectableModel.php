<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laradigs\Tweaker\CanTweak;

class AllFieldsAreProjectableModel extends Model
{
    /** @use HasFactory<\Database\Factories\AllFieldsAreProjectableModelFactory> */
    use HasFactory;
    use CanTweak;

    protected $fillable = [
        'name',
    ];

    protected function getProjectableFields()
    {
        return ['id', 'name'];
    }
}
