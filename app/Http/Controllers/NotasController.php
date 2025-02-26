<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotasController extends Controller
{
    public function index()
    {
        // Por enquanto, exiba uma view simples ou retorne uma string
        return view('notas.index'); // Certifique-se de criar essa view
    }


    public function showPdf($id) {
        $pdfPath = storage_path('app/public/notas/'.$id.'.pdf');
        return response()->file($pdfPath);
    }
}
