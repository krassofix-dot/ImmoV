<?php

namespace App\Modules\System\Console;

use App\Modules\Parties\Enums\OccupancyType;
use App\Modules\Parties\Enums\PartyKind;
use App\Modules\Parties\Enums\PartyRole;
use App\Modules\Parties\Models\Party;
use App\Modules\Portfolio\Enums\OwnershipType;
use App\Modules\Portfolio\Enums\PropertyType;
use App\Modules\Portfolio\Enums\UsageType;
use App\Modules\Portfolio\Models\Country;
use App\Modules\Portfolio\Models\Property;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Legt die fünf bekannten Liegenschaften als Grundgerüst an.
 *
 * Enthält nur Angaben aus dem Auftrag und Daniels Klärungen (Phase 1).
 * Unbekanntes (Adressen, Mieternamen, Beginn der Eigennutzung) bleibt leer
 * und wird in der Oberfläche ergänzt. Mietparteien werden als Platzhalter
 * angelegt, damit keine Personendaten im Quellcode stehen.
 */
class SeedPortfolioCommand extends Command
{
    protected $signature = 'immov:seed-portfolio {--owner=Daniel : Name der Eigentümerpartei} {--force : Auch ausführen, wenn bereits Liegenschaften existieren}';

    protected $description = 'Grundgerüst des Portfolios (CH, ES, PT) anlegen';

    public function handle(): int
    {
        if (Property::withTrashed()->exists() && ! $this->option('force')) {
            $this->warn('Es existieren bereits Liegenschaften. Abbruch (mit --force trotzdem ausführen).');

            return self::FAILURE;
        }

        DB::transaction(function () {
            $ch = Country::where('code', 'CH')->firstOrFail();
            $es = Country::where('code', 'ES')->firstOrFail();
            $pt = Country::where('code', 'PT')->firstOrFail();

            $owner = Party::create([
                'kind' => PartyKind::Person, 'role' => PartyRole::Owner,
                'last_name' => (string) $this->option('owner'),
            ]);

            // 1 Riet – Neftenbach: Einfamilienhaus, selbst genutzt
            $this->property($ch, 'Riet – Neftenbach', PropertyType::SingleFamilyHouse, null, ['city' => 'Neftenbach'], [
                ['Einfamilienhaus', null, UsageType::Apartment, OccupancyType::OwnerOccupied, $owner],
            ]);

            // 2 Reichenbach: MFH mit drei Einheiten, PV-Anlage, ZEV über Zevvy
            $tenantEg = Party::create(['kind' => PartyKind::Person, 'role' => PartyRole::Tenant, 'last_name' => 'Mietpartei EG Reichenbach', 'notes' => 'Platzhalter – Namen und Kontaktdaten ergänzen.']);
            $tenantOg = Party::create(['kind' => PartyKind::Person, 'role' => PartyRole::Tenant, 'last_name' => 'Mietpartei OG Reichenbach', 'notes' => 'Platzhalter – Namen und Kontaktdaten ergänzen.']);
            $this->property($ch, 'Reichenbach', PropertyType::MultiFamilyHouse, null, [
                'street' => 'Buchholzstrasse 67', 'postal_code' => '3713', 'city' => 'Reichenbach im Kandertal',
                'energy_supply' => 'Photovoltaikanlage; Zusammenschluss zum Eigenverbrauch (ZEV), Stromabrechnung über Zevvy (Standort «Oberland»).',
            ], [
                // Mietverträge HEV Bern: EG ab 1.2.2018, OG ab 1.4.2018
                ['EG', 'EG', UsageType::Studio, OccupancyType::Rental, $tenantEg, '130.00', '1100.00', '2018-02-01'],
                ['OG', 'OG', UsageType::Apartment, OccupancyType::Rental, $tenantOg, '130.00', '1500.00', '2018-04-01'],
                ['DG', 'DG', UsageType::Apartment, OccupancyType::OwnerOccupied, $owner],
            ]);

            // 3 Neuenhof: Stockwerkeigentum, selbst genutzt
            $this->property($ch, 'Neuenhof', PropertyType::CondominiumApartment, OwnershipType::Condominium, ['city' => 'Neuenhof'], [
                ['Wohnung', null, UsageType::Apartment, OccupancyType::OwnerOccupied, $owner],
            ]);

            // 4 Spanien: Mehrfamilienhaus mit Ferienhaus und Ferien-Studio, privat genutzt
            $this->property($es, 'Spanien – Mehrfamilienhaus', PropertyType::MultiFamilyHouse, null, [], [
                ['Ferienhaus', null, UsageType::HolidayHome, OccupancyType::PrivateUse, $owner],
                ['Ferien-Studio', null, UsageType::HolidayStudio, OccupancyType::PrivateUse, $owner],
            ]);

            // 5 Portugal: Stockwerkeigentum, selbst genutzt
            $this->property($pt, 'Portugal – Stockwerkeigentum', PropertyType::CondominiumApartment, OwnershipType::Condominium, [], [
                ['Wohnung', null, UsageType::Apartment, OccupancyType::OwnerOccupied, $owner],
            ]);
        });

        $this->info('Portfolio angelegt: 5 Liegenschaften, 8 Einheiten.');

        return self::SUCCESS;
    }

    /** @param list<array{0:string,1:?string,2:UsageType,3:OccupancyType,4:Party,5?:string,6?:string,7?:string}> $units */
    private function property(Country $country, string $name, PropertyType $type, ?OwnershipType $ownership, array $attrs, array $units): void
    {
        $property = Property::create(array_merge([
            'country_id' => $country->id,
            'name' => $name,
            'property_type' => $type,
            'ownership_type' => $ownership,
        ], $attrs));

        $building = $property->buildings()->create(['name' => 'Hauptgebäude']);

        foreach ($units as $u) {
            $unit = $building->units()->create(['name' => $u[0], 'floor' => $u[1], 'usage_type' => $u[2]]);
            $unit->occupancies()->create([
                'type' => $u[3],
                'party_id' => $u[4]->id,
                'advance_payment' => $u[5] ?? null,
                'rent_net' => $u[6] ?? null,
                'start_date' => $u[7] ?? null,
                'currency' => isset($u[5]) ? $country->default_currency : null,
                'notes' => isset($u[7]) ? null : 'Beginn offen – bitte ergänzen.',
            ]);
        }
    }
}
