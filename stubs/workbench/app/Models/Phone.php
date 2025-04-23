<?php

namespace Workbench\App\Models;

use Workbench\App\Models\User;
use RGalura\ApiIgniter\ApiIgniter;
use RGalura\ApiIgniter\Expandable;
use RGalura\ApiIgniter\Projectable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Phone extends Model
{
    /** @use HasFactory<\Database\Factories\PhoneFactory> */
    use HasFactory;
    use ApiIgniter, Expandable, Projectable;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
