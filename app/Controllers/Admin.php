<?php

namespace App\Controllers;

use App\Models\LinkModel;
use App\Controllers\BaseController;
use Myth\Auth\Models\LoginModel;

class Admin extends BaseController
{
    public function index()
    {
        return view('admin/index');
    }
}