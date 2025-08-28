<?php

namespace App\Http\Controllers;

use App\Models\PublishSchedule;              // <— AQUÍ
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = PublishSchedule::where('user_id', Auth::id())
            ->orderBy('day')->orderBy('time')
            ->get();

        return view('schedules.index', compact('schedules'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'day'  => ['required','in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo'],
            'time' => ['required','date_format:H:i'],
        ]);

        PublishSchedule::updateOrCreate(
            ['user_id' => Auth::id(), 'day' => $data['day']],
            ['time'    => $data['time']]
        );

        return back()->with('status', 'Horario guardado');
    }

    public function destroy(PublishSchedule $schedule)
    {
        abort_unless($schedule->user_id === Auth::id(), 403);
        $schedule->delete();

        return back()->with('status', 'Horario eliminado');
    }
}
