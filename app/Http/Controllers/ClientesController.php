<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientesController extends Controller
{
    public function index()
    {
        // Por enquanto, exiba uma view simples para clientes
        return view('clientes.index');
    }
}
