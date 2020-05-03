<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IPCHomeController extends Controller
{
    protected $host;

    public function __construct()
    { 
        $this->host = $_SERVER['HTTP_HOST'];
    }

    public function ipc_home()
    {
        if ($this->host == 'idh.isuzuphil.com') 
            return redirect()->away('http://portal.isuzuphil.com/ipc_central/main_home.php');
        else if ($this->host == 'ecommerce4')
            return redirect()->away('http://ecommerce5/ipc_central/main_home.php');
    }
}
