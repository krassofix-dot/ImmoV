<?php

namespace App\Modules\Portfolio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/** Gebäude */
class Building extends Model implements AuditableContract
{
    use Auditable, SoftDeletes;

    protected $fillable = ['property_id', 'name', 'construction_year', 'floors', 'notes'];

    protected function casts(): array
    {
        return ['construction_year' => 'integer', 'floors' => 'integer'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class)->orderBy('id');
    }
}
