<?php

namespace App\Http\Controllers\Blog\Admin;

use App\Http\Controllers\Blog\BaseController as MainBaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

abstract class AdminBaseController extends MainBaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status');
    }
}
