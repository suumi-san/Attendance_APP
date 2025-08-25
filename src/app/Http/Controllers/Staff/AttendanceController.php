<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\CorrectionRequest;
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

        // 保存前の値を取得（必要に応じて各フィールド）
        $beforeValues = [
            'clock_in'  => $attendance->clock_in?->format('H:i'),
            'clock_out' => $attendance->clock_out?->format('H:i'),
            'note'      => $attendance->note,
        ];

        // Attendance を更新
        $attendance->update([
            'clock_in'  => $request->input('clock_in'),
            'clock_out' => $request->input('clock_out'),
            'note'      => $request->input('note'),
            'status'    => 'pending', // 修正申請扱い
        ]);

        // 修正申請レコードを作成
        $attendance->correctionRequests()->create([
            'field'        => 'all', // 変更対象が複数なら 'all'
            'before_value' => json_encode($beforeValues),
            'after_value'  => json_encode([
                'clock_in'  => $request->input('clock_in'),
                'clock_out' => $request->input('clock_out'),
                'note'      => $request->input('note'),
            ]),
            'reason'       => $request->input('reason', 'ユーザーによる修正申請'),
            'requested_at' => now(),
            'status'       => \App\Models\CorrectionRequest::STATUS_PENDING,
        ]);

        // 休憩の更新
        $breakStarts = $request->input('break_start', []);
        $breakEnds   = $request->input('break_end', []);

        // 既存休憩を削除
        $attendance->breaks()->delete();

        foreach ($breakStarts as $index => $start) {
            $end = $breakEnds[$index] ?? null;

            if (!$start && !$end) {
                continue; // 空欄スキップ
            }

            $startTime = $start ? Carbon::parse($start) : null;
            $endTime   = $end   ? Carbon::parse($end)   : null;
            $duration  = ($startTime && $endTime) ? $endTime->diffInMinutes($startTime) : 0;

            $attendance->breaks()->create([
                'break_start'      => $startTime,
                'break_end'        => $endTime,
                'duration_minutes' => $duration,
            ]);
        }

        // 合計休憩時間を更新
        $attendance->updateBreakTotal();

        return redirect()->route('attendance.detail', ['id' => $attendance->id])
            ->with('status', '更新しました。承認待ちです');
    }

    public function requestList()
    {
        // 承認待ちリスト
        $pendingRequests = CorrectionRequest::with(['attendance.user'])
            ->where('status', 'pending')
            ->orderBy('requested_at', 'desc')
            ->paginate(20, ['*'], 'pending_page'); // ページネーション区別用

        // 承認済みリスト
        $approvedRequests = CorrectionRequest::with(['attendance.user'])
            ->where('status', 'approved')
            ->orderBy('requested_at', 'desc')
            ->paginate(20, ['*'], 'approved_page'); // ページネーション区別用

        return view('staff.request', compact('pendingRequests', 'approvedRequests'));
    }
}
