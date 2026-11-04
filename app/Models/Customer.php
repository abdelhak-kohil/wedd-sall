<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    public const SOURCE_FACEBOOK = 'FACEBOOK';
    public const SOURCE_INSTAGRAM = 'INSTAGRAM';
    public const SOURCE_TIKTOK = 'TIKTOK';
    public const SOURCE_GOOGLE = 'GOOGLE';
    public const SOURCE_RECOMMENDATION = 'RECOMMENDATION';
    public const SOURCE_WALK_IN = 'WALK_IN';
    public const SOURCE_OTHER = 'OTHER';

    public const SOURCES = [
        self::SOURCE_FACEBOOK => '📱 Facebook',
        self::SOURCE_INSTAGRAM => '📸 Instagram',
        self::SOURCE_TIKTOK => '🎵 TikTok',
        self::SOURCE_GOOGLE => '🔍 Google',
        self::SOURCE_RECOMMENDATION => '🤝 Recommandation',
        self::SOURCE_WALK_IN => '🚶 Visite directe',
        self::SOURCE_OTHER => '📌 Autre',
    ];

    protected $fillable = [
        'business_id',
        'customer_code',
        'full_name',
        'phone',
        'second_phone',
        'whatsapp_number',
        'email',
        'wilaya_id',
        'commune_id',
        'address_line',
        'bride_name',
        'groom_name',
        'preferred_language',
        'source',
        'internal_notes',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function wilaya(): BelongsTo
    {
        return $this->belongsTo(Wilaya::class, 'wilaya_id');
    }

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    // WhatsApp deep link helper
    public function whatsappLink(string $message = ''): string
    {
        $number = $this->whatsapp_number ?? $this->phone;
        $number = preg_replace('/\D/', '', $number);
        if (str_starts_with($number, '0')) {
            $number = '213' . substr($number, 1);
        }
        return 'https://wa.me/' . $number . ($message ? '?text=' . urlencode($message) : '');
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->bride_name && $this->groom_name) {
            return "{$this->groom_name} & {$this->bride_name}";
        }
        return $this->full_name;
    }
}
