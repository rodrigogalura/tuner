<?php

namespace Workbench\App\Http\Controllers;

use Illuminate\Http\Request;
use RGalura\ApiIgniter\Contracts\ApiIgniterInterface;

class UserController
{
    public function __construct(private ApiIgniterInterface $user)
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->user->send();
    }
}
