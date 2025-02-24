<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConfiguracoesController extends Controller
{
    public function index()
    {
        // Por enquanto, exiba uma view simples ou retorne uma string
        return view('cofiguracoes.index'); // Certifique-se de criar essa view
    }
}
