<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class PostController extends BaseController
{
        private $loggedUser;

    public function __construct(){
        $this->middleware('auth:api');

        $this->loggedUser = Auth::user();
    }
}
