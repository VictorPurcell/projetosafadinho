<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TitulosController extends Controller
{
    public function index()
    {
        return view('titulos.index');
    }
}
