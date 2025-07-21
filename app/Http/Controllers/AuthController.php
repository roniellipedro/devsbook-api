<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller as BaseController;

class AuthController extends BaseController
{
    public function __construct(){
        $this->middleware('auth:api',[
            'except' =>[
            'login',
            'create',
            'unauthorized'
            ]
        ]);
    }
}
