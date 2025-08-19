@extends('layouts.default')

@section('title','勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/detail.css')  }}">
@endsection

@section('content')
@include('components.header')
<div class="attendance-detail">
    <div class="detail-title">
        <h1 class="title">勤怠詳細</h1>
    </div>
    <form method="POST" action="{{ route('attendance.detail.update', ['id' => $attendance->id]) }}">
        @csrf
        @method('POST')

        <table class="detail-table">
            <tr>
                <th>名前</th>
                <td>{{ $attendance->user->name }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td>{{ $attendance->work_date->format('Y年 n月 j日') }}</td>
            </tr>

            <tr>
                <th>出勤・退勤</th>
                <td>
                    <input type="time" name="clock_in" value="{{ old('clock_in', $attendance->clock_in) }}" class="input" {{ $attendance->is_editable ? '' : 'disabled' }}>
                    〜
                    <input type="time" name="clock_out" value="{{ old('clock_out', $attendance->clock_out) }}" class="input" {{ $attendance->is_editable ? '' : 'disabled' }}>
                </td>
            </tr>

            <tr>
                <th>休憩</th>
                <td>
                    <input type="time" name="break_start" value="{{ old('break_start', $attendance->break_start) }}" class="input" {{ $attendance->is_editable ? '' : 'disabled' }}>
                    〜
                    <input type="time" name="break_end" value="{{ old('break_end', $attendance->break_end) }}" class="input" {{ $attendance->is_editable ? '' : 'disabled' }}>
                </td>
            </tr>

            <tr>
                <th>休憩2</th>
                <td>
                    <input type="time" name="break2_start" value="{{ old('break2_start', $attendance->break2_start) }}" class="input" {{ $attendance->is_editable ? '' : 'disabled' }}>
                    〜
                    <input type="time" name="break2_end" value="{{ old('break2_end', $attendance->break2_end) }}" class="input" {{ $attendance->is_editable ? '' : 'disabled' }}>
                </td>
            </tr>

            <tr>
                <th>備考</th>
                <td>
                    <textarea name="note" class="textarea" {{ $attendance->is_editable ? '' : 'disabled' }}>
                    {{ old('note', $attendance->note) }}
                    </textarea>
                </td>
            </tr>
        </table>

        <div class="button-wrapper">
            @if ($attendance->is_editable)
            <button type="submit" class="btn-primary">修正</button>
            @else
            <p style="color: red;">＊承認待ちのため修正はできません</p>
            @endif
        </div>
    </form>
</div>


@endsection