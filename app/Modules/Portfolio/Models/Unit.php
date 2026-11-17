<?php

namespace App\Modules\Portfolio\Models;

use App\Modules\Parties\Models\Occupancy;
use App\Modules\Portfolio\Enums\UsageType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/** Einheit (Wohnung, Studio, Ferienhaus …) */
class Unit extends Model implements AuditableContract
{
    use Auditable, SoftDeletes;

    protected $fillable = ['building_id', 'name', 'floor', 'area_m2', 'rooms', 'usage_type', 'notes'];

    protected function casts(): array
    {
        return [
            'usage_type' => UsageType::class,
            'area_m2' => 'decimal:2',
            'rooms' => 'decimal:1',
        ];
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function occupancies(): HasMany
    {
        return $this->hasMany(Occupancy::class)->orderByDesc('start_date');
    }

    /** Nutzung, die am Stichtag gilt (Standard: heute). */
    public function occupancyOn(?Carbon $date = null): ?Occupancy
    {
        $date ??= Carbon::today();

        return $this->occupancies
            ->first(fn (Occupancy $o) => $o->isActiveOn($date));
    }
}
