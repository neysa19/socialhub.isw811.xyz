<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedules; 

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedules::all();
        return view('schedules.index', compact('schedules'));
    }

}
