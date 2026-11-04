<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commune extends Model
{
    public $timestamps = false;

    protected $fillable = ['wilaya_id', 'name_fr', 'name_ar', 'postcode'];

    public function wilaya(): BelongsTo
    {
        return $this->belongsTo(Wilaya::class, 'wilaya_id');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
