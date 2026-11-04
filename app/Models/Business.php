<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_fr',
        'name_ar',
        'slug',
        'phone',
        'whatsapp',
        'email',
        'wilaya',
        'commune',
        'address_line',
        'nif',
        'nis',
        'rc',
        'logo_path',
        'currency',
        'timezone',
        'settings',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
