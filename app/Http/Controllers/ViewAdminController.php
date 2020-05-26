<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function perfil()
    {
        return view('admin.perfil');
    }    
}
