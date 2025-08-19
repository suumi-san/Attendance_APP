@extends('layouts.default')

@section('title','勤怠登録')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance.css')  }}">
@endsection

@section('content')

@include('components.header')
<div class="container">

    @if ($status === 'before_work')
    <p class="status">勤務外</p>
    <p class="today">{{ now()->format('Y年m月d日') }}（{{ ['日','月','火','水','木','金','土'][now()->dayOfWeek] }}）</p>
    <p id="current-time" class="time"></p>
    <form action="{{ route('attendance.start') }}" method="POST">
        @csrf
        <button type="submit" class="attendance-button">出勤</button>
    </form>

    @elseif ($status === 'working')
    <p class="status">出勤中</p>
    <p class="today">{{ now()->format('Y年m月d日') }}&#40;{{ ['日','月','火','水','木','金','土'][now()->dayOfWeek] }}&#41;</p>
    <p id="current-time" class="time"></p>
    <div class="buttons">
        <form action="{{ route('attendance.finish') }}" method="POST">
            @csrf
            <button type="submit" class="attendance-button">退勤</button>
        </form>
        <form action="{{ route('attendance.break_start') }}" method="POST">
            @csrf
            <button type="submit" class="break-button">休憩入</button>
        </form>
    </div>

    @elseif ($status === 'on_break')
    <p class="status">休憩中</p>
    <p class="today">{{ now()->format('Y年m月d日') }}（{{ ['日','月','火','水','木','金','土'][now()->dayOfWeek] }}）</p>
    <p id="current-time" class="time"></p>
    <form action="{{ route('attendance.break_end') }}" method="POST">
        @csrf
        <button type="submit" class="break-button">休憩戻</button>
    </form>

    @elseif ($status === 'after_work')
    <p class="status">退勤済</p>
    <p class="today">{{ now()->format('Y年m月d日') }}({{ ['日','月','火','水','木','金','土'][now()->dayOfWeek] }})</p>
    <p id="current-time" class="time"></p>
    <p class="message">お疲れ様でした。</p>
    @endif

</div>

<script>
    function updateTime() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('current-time').textContent = `${h}:${m}`;
    }
    setInterval(updateTime, 1000);
    updateTime();
</script>


@endsection