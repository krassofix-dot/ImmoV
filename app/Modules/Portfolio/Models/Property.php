<?php

namespace App\Modules\Portfolio\Models;

use App\Modules\Portfolio\Enums\OwnershipType;
use App\Modules\Portfolio\Enums\PropertyType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/** Liegenschaft */
class Property extends Model implements AuditableContract
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'country_id', 'name', 'street', 'postal_code', 'city', 'region',
        'ownership_type', 'property_type', 'construction_year',
        'plot_number', 'plot_area_m2', 'land_registry_info',
        'purchase_price', 'purchase_currency', 'purchase_date',
        'energy_supply', 'technical_systems', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'ownership_type' => OwnershipType::class,
            'property_type' => PropertyType::class,
            'construction_year' => 'integer',
            'plot_area_m2' => 'decimal:2',
            'purchase_price' => 'decimal:2',
            'purchase_date' => 'date',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class)->orderBy('name');
    }

    public function units(): HasManyThrough
    {
        return $this->hasManyThrough(Unit::class, Building::class);
    }

    public function getAddressLineAttribute(): string
    {
        return trim(collect([$this->street, trim($this->postal_code.' '.$this->city)])->filter()->implode(', '));
    }
}
