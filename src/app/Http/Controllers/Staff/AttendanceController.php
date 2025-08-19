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
            ->keyBy(function ($item) {
                return $item->work_date->format('Y-m-d');
            });

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

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        if (!$attendance->is_editable) {
            return redirect()->back()->with('error', '承認待ちのため修正できません');
        }

        $attendance->clock_in    = $request->input('clock_in');
        $attendance->clock_out   = $request->input('clock_out');
        $attendance->break_start = $request->input('break_start');
        $attendance->break_end   = $request->input('break_end');
        $attendance->break2_start = $request->input('break2_start');
        $attendance->break2_end   = $request->input('break2_end');
        $attendance->note = $request->input('note');
        $attendance->status = 'pending';

        $attendance->save();

        return redirect()->route('attendance.detail', ['id' => $attendance->id])
            ->with('status', '更新しました。承認待ちです');
    }
}
