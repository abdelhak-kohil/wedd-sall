<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    public const STATUS_INQUIRY = 'INQUIRY';
    public const STATUS_HOLD = 'TEMPORARY_HOLD';
    public const STATUS_CONFIRMED = 'CONFIRMED';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_CANCELLED = 'CANCELLED';
    public const STATUS_EXPIRED = 'EXPIRED';
    public const STATUS_ARCHIVED = 'ARCHIVED';

    public const PAYMENT_UNPAID = 'UNPAID';
    public const PAYMENT_DEPOSIT_PAID = 'DEPOSIT_PAID';
    public const PAYMENT_PARTIALLY_PAID = 'PARTIALLY_PAID';
    public const PAYMENT_PAID = 'PAID';
    public const PAYMENT_REFUNDED = 'REFUNDED';

    protected $fillable = [
        'business_id',
        'reservation_number',
        'customer_id',
        'venue_space_id',
        'time_slot_id',
        'package_id',
        'customer_name',
        'customer_phone',
        'bride_name',
        'groom_name',
        'event_type',
        'event_date',
        'start_time',
        'end_time',
        'expected_guests',
        'status',
        'payment_status',
        'hold_expires_at',
        'total_price',
        'deposit_required',
        'deposit_paid',
        'total_paid',
        'remaining_balance',
        'internal_notes',
        'created_by',
        'cancelled_at',
        'cancellation_reason',
        'cancelled_by',
        'refund_amount',
    ];

    protected function casts(): array
    {
        return [
            'event_date'      => 'date',
            'hold_expires_at' => 'datetime',
            'cancelled_at'    => 'datetime',
            'total_price'     => 'decimal:2',
            'deposit_required'=> 'decimal:2',
            'deposit_paid'    => 'decimal:2',
            'total_paid'      => 'decimal:2',
            'remaining_balance'=> 'decimal:2',
            'expected_guests' => 'integer',
        ];
    }


    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function space(): BelongsTo
    {
        return $this->belongsTo(VenueSpace::class, 'venue_space_id');
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderByDesc('created_at');
    }

    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EventTask::class)->orderBy('sort_order');
    }

    public function vendors(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EventVendor::class)->orderBy('category');
    }

    public function timelines(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EventTimeline::class)->orderBy('time');
    }

    public function getTasksProgressAttribute(): int
    {
        $total = $this->tasks()->count();
        if ($total === 0) {
            return 0;
        }
        $done = $this->tasks()->where('status', EventTask::STATUS_COMPLETED)->count();
        return (int) round(($done / $total) * 100);
    }

    public function syncFinancials(): void
    {
        $depositPaid = (float) $this->payments()->where('payment_type', Payment::TYPE_DEPOSIT)->sum('amount');
        $totalPaid = (float) $this->payments()->where('payment_type', '!=', Payment::TYPE_REFUND)->sum('amount') 
                   - (float) $this->payments()->where('payment_type', Payment::TYPE_REFUND)->sum('amount');

        $remaining = max(0, (float) $this->total_price - $totalPaid);

        $paymentStatus = self::PAYMENT_UNPAID;
        if ($remaining <= 0 && $this->total_price > 0) {
            $paymentStatus = self::PAYMENT_PAID;
        } elseif ($totalPaid > $depositPaid) {
            $paymentStatus = self::PAYMENT_PARTIALLY_PAID;
        } elseif ($totalPaid > 0) {
            $paymentStatus = self::PAYMENT_DEPOSIT_PAID;
        }

        $this->update([
            'deposit_paid' => $depositPaid,
            'total_paid' => $totalPaid,
            'remaining_balance' => $remaining,
            'payment_status' => $paymentStatus,
        ]);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function isTemporaryHold(): bool
    {
        return $this->status === self::STATUS_HOLD;
    }

    public function isHoldExpired(): bool
    {
        return $this->isTemporaryHold() && $this->hold_expires_at && $this->hold_expires_at->isPast();
    }

    public function getHoldHoursRemainingAttribute(): ?int
    {
        if (!$this->isTemporaryHold() || !$this->hold_expires_at) {
            return null;
        }

        return max(0, (int) now()->diffInHours($this->hold_expires_at, false));
    }

    public function getTimeRangeAttribute(): string
    {
        return substr($this->start_time, 0, 5) . ' - ' . substr($this->end_time, 0, 5);
    }

    public function statusBadge(): array
    {
        return match ($this->status) {
            self::STATUS_HOLD => [
                'label' => $this->isHoldExpired() ? 'Option expirée' : 'Option temporaire',
                'class' => $this->isHoldExpired() ? 'bg-amber-950/60 text-amber-300 border border-amber-800' : 'bg-amber-500/10 text-amber-400 border border-amber-500/30',
            ],
            self::STATUS_CONFIRMED => [
                'label' => 'Confirmé',
                'class' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30',
            ],
            self::STATUS_COMPLETED => [
                'label' => 'Terminé',
                'class' => 'bg-blue-500/10 text-blue-400 border border-blue-500/30',
            ],
            self::STATUS_CANCELLED => [
                'label' => 'Annulé',
                'class' => 'bg-rose-500/10 text-rose-400 border border-rose-500/30',
            ],
            self::STATUS_EXPIRED => [
                'label' => 'Expiré',
                'class' => 'bg-slate-700/60 text-slate-400 border border-slate-600',
            ],
            default => [
                'label' => $this->status,
                'class' => 'bg-slate-800 text-slate-300',
            ],
        };
    }
}
