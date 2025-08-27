<?php
// app/Http/Controllers/ScheduleController.php
// app/Http/Controllers/ScheduleController.php
namespace App\Http\Controllers;

use App\Models\PublishSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;   // <- IMPORTA ESTO

class ScheduleController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $schedules = PublishSchedule::where('user_id', $userId)
            ->orderBy('weekday')
            ->orderBy('time')
            ->get();

        return view('schedules.index', compact('schedules'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'weekday' => ['required','integer','between:0,6'],
            'time'    => ['required','date_format:H:i'],
        ]);

        PublishSchedule::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'weekday' => $data['weekday'],
                'time'    => $data['time'],
            ],
            []
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
