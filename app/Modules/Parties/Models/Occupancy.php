<?php

namespace App\Modules\Parties\Models;

use App\Modules\Parties\Enums\OccupancyType;
use App\Modules\Portfolio\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * Nutzung einer Einheit über einen Zeitraum.
 * Ein leeres Startdatum bedeutet «seit unbekannt», ein leeres Enddatum «laufend».
 */
class Occupancy extends Model implements AuditableContract
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'unit_id', 'party_id', 'type', 'start_date', 'end_date',
        'rent_net', 'advance_payment', 'currency', 'contract_reference', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => OccupancyType::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'rent_net' => 'decimal:2',
            'advance_payment' => 'decimal:2',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function isActiveOn(Carbon $date): bool
    {
        return ($this->start_date === null || $this->start_date->lte($date))
            && ($this->end_date === null || $this->end_date->gte($date));
    }

    public function scopeActiveOn(Builder $query, Carbon $date): Builder
    {
        return $query
            ->where(fn ($q) => $q->whereNull('start_date')->orWhereDate('start_date', '<=', $date))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhereDate('end_date', '>=', $date));
    }
}
