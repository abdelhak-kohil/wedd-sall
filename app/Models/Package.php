<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name_fr',
        'name_ar',
        'description_fr',
        'description_ar',
        'base_price',
        'price_per_guest',
        'included_items',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'price_per_guest' => 'decimal:2',
            'included_items' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }


    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function calculateTotal(int $guests): float
    {
        return (float) ($this->base_price + ($this->price_per_guest * $guests));
    }
}
