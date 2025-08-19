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

    protected $casts = [
        'work_date'        => 'date',
        'clock_in'         => 'datetime:H:i:s',
        'clock_out'        => 'datetime:H:i:s',
        'break_start_time' => 'datetime:H:i:s',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function correctionRequests(): HasMany
    {
        return $this->hasMany(CorrectionRequest::class);
    }


    public function getClockInFormattedAttribute()
    {
        return $this->clock_in ? $this->clock_in->format('H:i') : '';
    }


    public function getClockOutFormattedAttribute()
    {
        return $this->clock_out ? $this->clock_out->format('H:i') : '';
    }


    public function getBreakFormattedAttribute()
    {
        $breakMin = $this->break_time ?? 0;
        return sprintf('%d:%02d', intdiv($breakMin, 60), $breakMin % 60);
    }


    public function getTotalFormattedAttribute()
    {
        if ($this->clock_in && $this->clock_out) {
            $totalMin = $this->clock_out->diffInMinutes($this->clock_in) - ($this->break_time ?? 0);
            return sprintf('%d:%02d', intdiv($totalMin, 60), $totalMin % 60);
        }
        return '';
    }


    public function getWeekdayJpAttribute()
    {
        $weekArray = ['日', '月', '火', '水', '木', '金', '土'];
        return $weekArray[$this->work_date->dayOfWeek];
    }

    public function getIsEditableAttribute(): bool
    {
        $latestRequest = $this->correctionRequests()->latest()->first();
        return !$latestRequest || !$latestRequest->is_pending;
    }
}
