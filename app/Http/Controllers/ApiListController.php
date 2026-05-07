<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiListController extends Controller
{
    public function index()
    {
        return view('admin.api_list.index');
    }
}
