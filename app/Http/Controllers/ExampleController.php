<?php

namespace App\Http\Controllers;

use App\Helpers\RestCurl;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
      return RestCurl::exec('GET','http://localhost/acv-dev/4cv-svc-4cc0unt/public/',[],'');
    }

    //
}
