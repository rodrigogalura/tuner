<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laradigs\Tweaker\CanTweak;

class InvalidProjectableModel extends Model
{
    use CanTweak;

    /** @use HasFactory<\Database\Factories\InvalidProjectableModelFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected function getProjectableFields()
    {
        return ['email'];
    }
}
