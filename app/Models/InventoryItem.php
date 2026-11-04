<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    public const CATEGORY_FURNITURE    = 'furniture';
    public const CATEGORY_TABLEWARE    = 'tableware';
    public const CATEGORY_LINEN        = 'linen';
    public const CATEGORY_AUDIO        = 'audio';
    public const CATEGORY_LIGHTING     = 'lighting';
    public const CATEGORY_DECORATION   = 'decoration';
    public const CATEGORY_OTHER        = 'other';

    public const CATEGORIES = [
        self::CATEGORY_FURNITURE  => ['label' => 'Mobilier',      'icon' => '🪑', 'color' => 'amber'],
        self::CATEGORY_TABLEWARE  => ['label' => 'Vaisselle',     'icon' => '🍽️', 'color' => 'sky'],
        self::CATEGORY_LINEN      => ['label' => 'Linge de table','icon' => '🧺', 'color' => 'violet'],
        self::CATEGORY_AUDIO      => ['label' => 'Son & Musique', 'icon' => '🎵', 'color' => 'pink'],
        self::CATEGORY_LIGHTING   => ['label' => 'Éclairage',     'icon' => '💡', 'color' => 'yellow'],
        self::CATEGORY_DECORATION => ['label' => 'Décoration',    'icon' => '🌸', 'color' => 'rose'],
        self::CATEGORY_OTHER      => ['label' => 'Autre',         'icon' => '📦', 'color' => 'slate'],
    ];

    protected $fillable = [
        'business_id',
        'name',
        'category',
        'unit',
        'quantity_total',
        'quantity_available',
        'quantity_alert',
        'description',
        'location',
        'unit_cost',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active'   => 'boolean',
            'unit_cost'   => 'decimal:2',
            'quantity_total'     => 'integer',
            'quantity_available' => 'integer',
            'quantity_alert'     => 'integer',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    // ── Computed Attributes ────────────────────────────────────────────────────

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category]['label'] ?? ucfirst($this->category);
    }

    public function getCategoryIconAttribute(): string
    {
        return self::CATEGORIES[$this->category]['icon'] ?? '📦';
    }

    public function getCategoryColorAttribute(): string
    {
        return self::CATEGORIES[$this->category]['color'] ?? 'slate';
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->quantity_available <= $this->quantity_alert && $this->quantity_available >= 0;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->quantity_available <= 0;
    }

    public function getTotalValueAttribute(): float
    {
        return ($this->unit_cost ?? 0) * $this->quantity_total;
    }
}
