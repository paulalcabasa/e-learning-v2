<?php

namespace App\Http\Controllers;

use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use RuntimeException;
use Illuminate\Http\Request;

class TestReportErrorController extends Controller
{
    public function test()
    {
        return Bugsnag::notifyException(new RuntimeException("Error occured wkwkwk"));
    }
}
