<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    public const TYPE_IN      = 'IN';       // Receiving new stock
    public const TYPE_OUT     = 'OUT';      // Removing / loss / damage
    public const TYPE_ADJUST  = 'ADJUST';   // Manual correction (inventory count)
    public const TYPE_RESERVE = 'RESERVE';  // Reserved for an event
    public const TYPE_RETURN  = 'RETURN';   // Returned after event

    public const TYPES = [
        self::TYPE_IN      => ['label' => 'Entrée stock',   'color' => 'emerald', 'icon' => '⬆️'],
        self::TYPE_OUT     => ['label' => 'Sortie / Perte', 'color' => 'rose',    'icon' => '⬇️'],
        self::TYPE_ADJUST  => ['label' => 'Correction',     'color' => 'amber',   'icon' => '✏️'],
        self::TYPE_RESERVE => ['label' => 'Réservé fête',   'color' => 'sky',     'icon' => '🎪'],
        self::TYPE_RETURN  => ['label' => 'Retour après',   'color' => 'teal',    'icon' => '↩️'],
    ];

    protected $fillable = [
        'business_id',
        'inventory_item_id',
        'reservation_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'reason',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity'        => 'integer',
            'quantity_before' => 'integer',
            'quantity_after'  => 'integer',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Computed ───────────────────────────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type]['label'] ?? $this->type;
    }

    public function getTypeColorAttribute(): string
    {
        return self::TYPES[$this->type]['color'] ?? 'slate';
    }

    public function getTypeIconAttribute(): string
    {
        return self::TYPES[$this->type]['icon'] ?? '📦';
    }

    /** Signed quantity for display (+10 or -5) */
    public function getSignedQuantityAttribute(): string
    {
        $signed = in_array($this->type, [self::TYPE_IN, self::TYPE_RETURN])
            ? "+{$this->quantity}"
            : "-{$this->quantity}";

        if ($this->type === self::TYPE_ADJUST) {
            $diff = $this->quantity_after - $this->quantity_before;
            $signed = $diff >= 0 ? "+{$diff}" : (string)$diff;
        }

        return $signed;
    }
}
