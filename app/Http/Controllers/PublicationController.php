<?php

namespace App\Http\Controllers;
use App\Models\Publication;
use Illuminate\Support\Facades\Auth;

class PublicationController extends Controller
{
    public function index()
    {
        $publications = Publication::where('user_id', Auth::id())
            ->latest()->with('targets')->get();

        return view('publications.index', compact('publications'));
    }
}
