<?php

namespace App\Http\Controllers\Staff\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;


class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // メール未認証なら認証画面へ
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // 初回ログインなら last_login_at を更新
        if (!$user->last_login_at) {
            $user->last_login_at = now();
            $user->save();
            // 初回ログインでもこのページに残す場合はリダイレクト不要
            // もし別ページに飛ばしたいならここで redirect()->route('attendance') など
        }

        Carbon::setLocale('ja');
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('work_date', $today)
            ->first();

        // 状態判定
        if (!$attendance) {
            $status = 'before_work'; // 出勤前
        } elseif (!$attendance->clock_out) {
            $status = $attendance->break_time_flag ? 'on_break' : 'working';
        } else {
            $status = 'after_work'; // 退勤後
        }

        return view('staff.attendance', compact('status'));
    }

    public function startWork()
    {
        Attendance::create([
            'user_id' => Auth::id(),
            'work_date' => Carbon::today(),
            'clock_in' => Carbon::now()->format('H:i:s'),
            'break_time' => 0,
            'note' => null,
            'break_time_flag' => false,
            'break_start_time' => null,
        ]);

        return redirect()->route('attendance');
    }

    public function finishWork()
    {
        $attendance = $this->getTodayAttendance();

        // 休憩中に退勤する場合は休憩時間を加算
        if ($attendance->break_time_flag && $attendance->break_start_time) {
            $attendance->break_time += $this->calculateBreakMinutes($attendance->break_start_time);
        }

        $attendance->update([
            'clock_out' => Carbon::now()->format('H:i:s'),
            'break_time_flag' => false,
            'break_start_time' => null,
        ]);

        return redirect()->route('attendance');
    }

    public function startBreak()
    {
        $attendance = $this->getTodayAttendance();
        $attendance->update([
            'break_start_time' => Carbon::now()->format('H:i:s'),
            'break_time_flag' => true,
        ]);

        return redirect()->route('attendance');
    }

    public function endBreak()
    {
        $attendance = $this->getTodayAttendance();

        if ($attendance->break_start_time) {
            $attendance->break_time += $this->calculateBreakMinutes($attendance->break_start_time);
        }

        $attendance->update([
            'break_start_time' => null,
            'break_time_flag' => false,
        ]);

        return redirect()->route('attendance');
    }

    private function getTodayAttendance()
    {
        return Attendance::where('user_id', Auth::id())
            ->whereDate('work_date', Carbon::today())
            ->firstOrFail();
    }

    private function calculateBreakMinutes($breakStart)
    {
        return intdiv(
            Carbon::parse($breakStart)->diffInSeconds(Carbon::now()),
            60
        );
    }
}
