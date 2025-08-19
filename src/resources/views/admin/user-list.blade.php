@section('css')
<link rel="stylesheet" href="{{ asset('/css/list.css')  }}">
@endsection

@section('content')

@include('components.header')
<div class="attendance-wrapper">

    <h2 class="title">{{ $user->name }}さんの勤怠</h2>

    <div class="month-nav">
        <a href="#" class="prev-month">&larr; 前月</a>
        <span class="current-month">{{ $yearMonthLabel }}</span>
        <a href="#" class="next-month">翌月 &rarr;</a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
            <tr>
                <td>{{ \Carbon\Carbon::parse($attendance->work_date)->format('m/d(D)') }}</td>
                <td>{{ $attendance->clock_in }}</td>
                <td>{{ $attendance->clock_out }}</td>
                <td>{{ $attendance->break_time }}</td>
                <td><a href="#" class="detail-link">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="csv-button-area">
        <button class="csv-button">CSV出力</button>
    </div>
</div>

@endsection