<?php

namespace Workbench\App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laradigs\Tweaker\CanTweak;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use RGalura\ApiIgniter\ApiIgniter;
// use RGalura\ApiIgniter\BetweenFilterable;
// use RGalura\ApiIgniter\Expandable;
// use RGalura\ApiIgniter\Filterable;
// use RGalura\ApiIgniter\InFilterable;
// use RGalura\ApiIgniter\Projectable;
// use RGalura\ApiIgniter\Searchable;
// use RGalura\ApiIgniter\Sortable;

class User extends Authenticatable
{
    // use ApiIgniter, BetweenFilterable, Expandable, Filterable, InFilterable, Projectable, Searchable, Sortable;
    use CanTweak;
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
