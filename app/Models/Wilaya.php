<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilaya extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = ['id', 'code', 'name_fr', 'name_ar'];

    public function communes(): HasMany
    {
        return $this->hasMany(Commune::class, 'wilaya_id');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'wilaya_id');
    }
}
