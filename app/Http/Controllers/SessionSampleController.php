<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionSampleController extends Controller
{
    public function flush_session(Request $request)
    {
        $request->session()->flush();

        return redirect()
            ->away('http://ecommerce5/ipc_central/php_processors/proc_logout.php');
    }
}
