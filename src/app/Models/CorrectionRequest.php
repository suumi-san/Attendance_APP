<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorrectionRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING   = 'pending';    // 承認待ち
    public const STATUS_APPROVED  = 'approved';   // 承認済み
    public const STATUS_REJECTED  = 'rejected';   // 却下
    public const STATUS_CORRECTED = 'corrected';  // 修正済み

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
        self::STATUS_CORRECTED,
    ];

    protected $fillable = [
        'attendance_id',
        'field',
        'before_value',
        'after_value',
        'reason',
        'requested_at',
        'status',
        'approver_id',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function getIsCorrectedAttribute(): bool
    {
        return $this->status === self::STATUS_CORRECTED;
    }
}
