<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule; 

class ScheduleController extends Controller
{
    public function index()
    {
        // Obtener todos los horarios del usuario autenticado (si los horarios están relacionados con usuarios)
        $schedules = Schedule::where('user_id', auth()->id())->get();

        // Retornar la vista con los horarios
        return view('schedules.index', compact('schedules'));
    }

}
