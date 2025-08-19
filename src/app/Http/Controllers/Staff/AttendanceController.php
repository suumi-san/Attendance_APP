<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $month = $request->input('month') ?? Carbon::now()->format('Y-m');
        $firstDay = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $lastDay  = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('work_date', [$firstDay->format('Y-m-d'), $lastDay->format('Y-m-d')])
            ->get()
            ->keyBy('work_date');

        $prevMonth = $firstDay->copy()->subMonth()->format('Y-m');
        $nextMonth = $firstDay->copy()->addMonth()->format('Y-m');

        return view('staff.list', [
            'user' => $user,
            'firstDay' => $firstDay,
            'lastDay' => $lastDay,
            'attendances' => $attendances,
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,
        ]);
    }

    public function show($id)
    {
        $attendance = Attendance::findOrFail($id);
        return view('staff.detail', compact('attendance'));
    }
}
