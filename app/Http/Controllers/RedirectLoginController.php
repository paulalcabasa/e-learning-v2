<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RedirectLoginController extends Controller
{
    public function login()
    {
        return redirect()->route('login');
    }
}
