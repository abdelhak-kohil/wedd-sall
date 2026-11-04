<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventVendor extends Model
{
    public const CATEGORY_TRAITEUR    = 'traiteur';
    public const CATEGORY_DJ          = 'dj_orchestre';
    public const CATEGORY_PHOTO       = 'photographe';
    public const CATEGORY_DECO        = 'decoration';
    public const CATEGORY_NEGAFA      = 'neguafa';
    public const CATEGORY_PATISSERIE  = 'patisserie';
    public const CATEGORY_AUTRE       = 'autre';

    public const CATEGORIES = [
        self::CATEGORY_TRAITEUR   => ['label' => 'Traiteur & Cuisine',      'icon' => '🍲', 'color' => 'amber'],
        self::CATEGORY_DJ         => ['label' => 'DJ & Orchestre',          'icon' => '🎧', 'color' => 'indigo'],
        self::CATEGORY_PHOTO      => ['label' => 'Photographe / Vidéo',     'icon' => '📸', 'color' => 'sky'],
        self::CATEGORY_DECO       => ['label' => 'Décoratrice florale',     'icon' => '💐', 'color' => 'rose'],
        self::CATEGORY_NEGAFA     => ['label' => 'Négafa (Ziana)',          'icon' => '👑', 'color' => 'purple'],
        self::CATEGORY_PATISSERIE => ['label' => 'Pâtisserie & Gâteau',     'icon' => '🎂', 'color' => 'pink'],
        self::CATEGORY_AUTRE      => ['label' => 'Autre prestataire',       'icon' => '🤝', 'color' => 'slate'],
    ];

    protected $fillable = [
        'business_id',
        'reservation_id',
        'category',
        'company_name',
        'contact_person',
        'phone',
        'arrival_time',
        'special_notes',
        'is_confirmed',
    ];

    protected function casts(): array
    {
        return [
            'is_confirmed' => 'boolean',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function getCategoryMetaAttribute(): array
    {
        return self::CATEGORIES[$this->category] ?? ['label' => $this->category, 'icon' => '🤝', 'color' => 'slate'];
    }

    public function getFormattedPhoneAttribute(): string
    {
        return preg_replace('/(\d{4})(\d{2})(\d{2})(\d{2})/', '$1 $2 $3 $4', $this->phone);
    }
}
