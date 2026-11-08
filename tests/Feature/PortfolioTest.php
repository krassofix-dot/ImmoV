<?php

namespace Tests\Feature;

use App\Modules\Parties\Models\Occupancy;
use App\Modules\Parties\Models\Party;
use App\Modules\Portfolio\Models\Building;
use App\Modules\Portfolio\Models\Country;
use App\Modules\Portfolio\Models\Property;
use App\Modules\Portfolio\Models\Unit;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PortfolioTest extends TestCase
{
    public function test_countries_are_seeded(): void
    {
        $this->assertSame(['CH', 'ES', 'PT'], Country::ordered()->pluck('code')->all());
        $this->assertSame('EUR', Country::where('code', 'ES')->value('default_currency'));
    }

    public function test_full_hierarchy_can_be_created_and_is_audited(): void
    {
        $user = $this->userWithRole('verwalter');
        $ch = Country::where('code', 'CH')->first();

        $this->actingAs($user)->post('/properties', [
            'country_id' => $ch->id, 'name' => 'Reichenbach', 'property_type' => 'multi_family_house',
            'purchase_price' => '850000.50', 'purchase_currency' => 'CHF',
        ])->assertRedirect();
        $property = Property::firstOrFail();
        $this->assertSame('850000.50', $property->purchase_price);

        $this->actingAs($user)->post("/properties/{$property->id}/buildings", ['name' => 'Hauptgebäude'])->assertRedirect();
        $building = Building::firstOrFail();

        $this->actingAs($user)->post("/buildings/{$building->id}/units", ['name' => 'EG', 'usage_type' => 'studio', 'area_m2' => '45.5'])->assertRedirect();
        $unit = Unit::firstOrFail();

        $party = Party::create(['kind' => 'person', 'role' => 'tenant', 'last_name' => 'Muster']);
        $this->actingAs($user)->post("/units/{$unit->id}/occupancies", [
            'type' => 'rental', 'party_id' => $party->id, 'start_date' => '2024-01-01',
            'advance_payment' => '130.00', 'currency' => 'CHF',
        ])->assertRedirect("/units/{$unit->id}");

        $this->actingAs($user)->get("/properties/{$property->id}")->assertOk()->assertSee('EG')->assertSee('Muster');
        $this->actingAs($user)->get("/units/{$unit->id}")->assertOk()->assertSee('130.00 CHF');

        foreach (['property', 'building', 'unit', 'occupancy'] as $type) {
            $this->assertDatabaseHas('audits', ['auditable_type' => $type, 'event' => 'created', 'user_id' => $user->id]);
        }
    }

    public function test_price_requires_currency(): void
    {
        $user = $this->userWithRole('verwalter');
        $ch = Country::where('code', 'CH')->first();

        $this->actingAs($user)->post('/properties', ['country_id' => $ch->id, 'name' => 'X', 'purchase_price' => '100'])
            ->assertSessionHasErrors('purchase_currency');
    }

    public function test_property_with_buildings_cannot_be_deleted(): void
    {
        $user = $this->userWithRole('verwalter');
        $property = Property::create(['country_id' => Country::first()->id, 'name' => 'P']);
        $property->buildings()->create(['name' => 'B']);

        $this->actingAs($user)->delete("/properties/{$property->id}")->assertSessionHasErrors('delete');
        $this->assertNotSoftDeleted($property);
    }

    public function test_occupancy_active_on_date(): void
    {
        $o = new Occupancy(['type' => 'rental', 'start_date' => '2024-01-01', 'end_date' => '2024-12-31']);

        $this->assertTrue($o->isActiveOn(Carbon::parse('2024-06-30')));
        $this->assertFalse($o->isActiveOn(Carbon::parse('2025-01-01')));
        $this->assertTrue((new Occupancy(['type' => 'owner_occupied']))->isActiveOn(Carbon::today()));
    }

    public function test_overlapping_occupancy_shows_warning(): void
    {
        $user = $this->userWithRole('verwalter');
        $property = Property::create(['country_id' => Country::first()->id, 'name' => 'P']);
        $unit = $property->buildings()->create(['name' => 'B'])->units()->create(['name' => 'U']);
        $party = Party::create(['kind' => 'person', 'role' => 'tenant', 'last_name' => 'A']);
        $unit->occupancies()->create(['type' => 'rental', 'party_id' => $party->id, 'start_date' => '2024-01-01']);

        $this->actingAs($user)->post("/units/{$unit->id}/occupancies", [
            'type' => 'rental', 'party_id' => $party->id, 'start_date' => '2025-01-01',
        ])->assertSessionHas('warning');

        $this->actingAs($user)->post("/units/{$unit->id}/occupancies", [
            'type' => 'vacant', 'start_date' => '2020-01-01', 'end_date' => '2020-12-31',
        ])->assertSessionMissing('warning');
    }

    public function test_dashboard_filters_by_country(): void
    {
        $user = $this->userWithRole('mitarbeiter');
        $this->artisan('immov:seed-portfolio')->assertSuccessful();

        $es = Country::where('code', 'ES')->first();
        $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee('Reichenbach');
        $response = $this->actingAs($user)->get('/dashboard?country='.$es->id)->assertOk();
        $this->assertSame(['properties' => 1, 'buildings' => 1, 'units' => 2, 'parties' => 3], $response->viewData('counts'));
        $this->assertSame(['CHF' => ['rent' => '2600.00', 'advance' => '260.00']], $this->actingAs($user)->get('/dashboard')->viewData('monthly'));
    }

    public function test_seed_portfolio_refuses_second_run(): void
    {
        $this->artisan('immov:seed-portfolio')->assertSuccessful();
        $this->artisan('immov:seed-portfolio')->assertFailed();
        $this->assertSame(5, Property::count());
        $this->assertSame(8, Unit::count());
    }

    public function test_language_switch(): void
    {
        $user = $this->userWithRole('mitarbeiter');

        $this->actingAs($user)->post('/locale', ['locale' => 'en'])->assertRedirect();
        $this->assertSame('en', $user->fresh()->locale);
        $this->actingAs($user->fresh())->get('/properties')->assertSee('Properties');
    }
}
