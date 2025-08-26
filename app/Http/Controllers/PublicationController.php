<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication;

class PublicationController extends Controller
{
    //
    public function index()
    {
        $publications = Publication::all(); // Recupera todas las publicaciones
        return view('publications.index', compact('publications'));
    }
}

