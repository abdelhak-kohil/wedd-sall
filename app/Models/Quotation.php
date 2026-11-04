<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quotation extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_SENT = 'SENT';
    public const STATUS_ACCEPTED = 'ACCEPTED';
    public const STATUS_EXPIRED = 'EXPIRED';
    public const STATUS_DECLINED = 'DECLINED';

    public const STATUSES = [
        self::STATUS_DRAFT => ['label_fr' => 'Brouillon', 'label_ar' => 'مسودة', 'class' => 'bg-slate-700 text-slate-300'],
        self::STATUS_SENT => ['label_fr' => 'Envoyé au client', 'label_ar' => 'تم الإرسال', 'class' => 'bg-blue-500/10 text-blue-400 border border-blue-500/30'],
        self::STATUS_ACCEPTED => ['label_fr' => 'Accepté ✓', 'label_ar' => 'مقبول ✓', 'class' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30'],
        self::STATUS_EXPIRED => ['label_fr' => 'Expiré', 'label_ar' => 'منتهي الصلاحية', 'class' => 'bg-amber-500/10 text-amber-400 border border-amber-500/30'],
        self::STATUS_DECLINED => ['label_fr' => 'Refusé', 'label_ar' => 'مرفوض', 'class' => 'bg-rose-500/10 text-rose-400 border border-rose-500/30'],
    ];

    protected $fillable = [
        'business_id',
        'quote_number',
        'customer_id',
        'customer_name',
        'customer_phone',
        'bride_name',
        'groom_name',
        'venue_space_id',
        'event_date',
        'start_time',
        'end_time',
        'expected_guests',
        'package_id',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'items',
        'total_price',
        'deposit_required',
        'status',
        'valid_until',
        'notes',
        'payment_terms',
        'converted_reservation_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'valid_until' => 'date',
            'subtotal' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_price' => 'decimal:2',
            'deposit_required' => 'decimal:2',
            'expected_guests' => 'integer',
            'items' => 'array',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function space(): BelongsTo
    {
        return $this->belongsTo(VenueSpace::class, 'venue_space_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class, 'converted_reservation_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusBadge(): array
    {
        return self::STATUSES[$this->status] ?? ['label_fr' => $this->status, 'class' => 'bg-slate-800 text-slate-300'];
    }

    public function getNetBeforeTaxAttribute(): float
    {
        return max(0, (float)$this->subtotal - (float)$this->discount_amount);
    }

    public function getBalanceDueAttribute(): float
    {
        return max(0, (float)$this->total_price - (float)$this->deposit_required);
    }

    public function getItemsListAttribute(): array
    {
        return is_array($this->items) ? $this->items : [];
    }
}
