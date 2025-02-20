<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class ApiController extends Controller
{
    use HasApiTokens;

    public function getUser(Request $request)
    {
        return response()->json(['user' => Auth::user()]);
    }
}

