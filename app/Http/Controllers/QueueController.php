<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication; 

class QueueController extends Controller
{
    public function index()
    {
        
        $queue = Publication::where('status', 'pending')->get();

       
        return view('queue.index', compact('queue'));
    }
}
