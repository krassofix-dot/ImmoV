<?php

namespace App\Modules\Parties\Models;

use App\Modules\Parties\Enums\PartyKind;
use App\Modules\Parties\Enums\PartyRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/** Partei: Mieter, Eigentümer, Lieferant … */
class Party extends Model implements AuditableContract
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'kind', 'role', 'first_name', 'last_name', 'company_name',
        'email', 'phone', 'street', 'postal_code', 'city', 'country_code', 'iban', 'notes',
    ];

    protected function casts(): array
    {
        return ['kind' => PartyKind::class, 'role' => PartyRole::class];
    }

    public function occupancies(): HasMany
    {
        return $this->hasMany(Occupancy::class)->orderByDesc('start_date');
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->kind === PartyKind::Company) {
            return (string) $this->company_name;
        }

        return trim($this->first_name.' '.$this->last_name);
    }
}
