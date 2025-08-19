<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'clock_in',
        'clock_out',
        'break_time',
        'note',
        'break_time_flag',
        'break_start_time',
    ];

    public const STATUS_NORMAL = 'normal';
    public const STATUS_CORRECTED = 'corrected';

    public const STATUSES = [
        self::STATUS_NORMAL,
        self::STATUS_CORRECTED,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function correctionRequests(): HasMany
    {
        return $this->hasMany(CorrectionRequest::class);
    }
    // Attendanceモデルに追記例
    public function getClockInFormattedAttribute()
    {
        return $this->clock_in ? \Carbon\Carbon::parse($this->clock_in)->format('H:i') : '-';
    }

    public function getClockOutFormattedAttribute()
    {
        return $this->clock_out ? \Carbon\Carbon::parse($this->clock_out)->format('H:i') : '-';
    }

    public function getBreakFormattedAttribute()
    {
        $breakMin = $this->break_time ?? 0;
        return sprintf('%d:%02d', intdiv($breakMin, 60), $breakMin % 60);
    }

    public function getTotalFormattedAttribute()
    {
        if ($this->clock_in && $this->clock_out) {
            $clockIn  = \Carbon\Carbon::parse($this->clock_in);
            $clockOut = \Carbon\Carbon::parse($this->clock_out);
            $totalMin = $clockOut->diffInMinutes($clockIn) - ($this->break_time ?? 0);
            return sprintf('%d:%02d', intdiv($totalMin, 60), $totalMin % 60);
        }
        return '-';
    }

    public function getWeekdayJpAttribute()
    {
        $weekArray = ['日', '月', '火', '水', '木', '金', '土'];
        return $weekArray[\Carbon\Carbon::parse($this->work_date)->dayOfWeek];
    }
}
