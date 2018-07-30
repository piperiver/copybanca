<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PasosController extends Controller
{
    public function index()
    { 
        return view('layouts-client.pasos.index');
    }
}
