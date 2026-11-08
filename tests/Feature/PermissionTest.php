<?php

namespace Tests\Feature;

use App\Modules\Portfolio\Models\Country;
use App\Modules\Portfolio\Models\Property;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    public function test_default_roles_exist(): void
    {
        $this->assertEqualsCanonicalizing(
            ['administrator', 'verwalter', 'mitarbeiter'],
            Role::pluck('name')->all(),
        );
    }

    public function test_mitarbeiter_cannot_reach_administration(): void
    {
        $user = $this->userWithRole('mitarbeiter');

        foreach (['/admin/users', '/admin/roles', '/admin/countries', '/admin/audit'] as $url) {
            $this->actingAs($user)->get($url)->assertForbidden();
        }
    }

    public function test_mitarbeiter_can_create_but_not_delete_properties(): void
    {
        $user = $this->userWithRole('mitarbeiter');
        $ch = Country::where('code', 'CH')->first();

        $this->actingAs($user)->post('/properties', ['country_id' => $ch->id, 'name' => 'Test'])->assertRedirect();
        $property = Property::where('name', 'Test')->firstOrFail();

        $this->actingAs($user)->delete("/properties/{$property->id}")->assertForbidden();
        $this->assertNotSoftDeleted($property);
    }

    public function test_verwalter_sees_audit_but_not_user_management(): void
    {
        $user = $this->userWithRole('verwalter');

        $this->actingAs($user)->get('/admin/audit')->assertOk();
        $this->actingAs($user)->get('/admin/users')->assertForbidden();
    }

    public function test_admin_can_change_role_permissions_and_change_is_audited(): void
    {
        $admin = $this->userWithRole('administrator');
        $role = Role::findByName('mitarbeiter');

        $this->actingAs($admin)->put('/admin/roles', [
            'permissions' => [$role->id => ['dashboard.view', 'portfolio.view']],
        ])->assertRedirect();

        $this->assertEqualsCanonicalizing(['dashboard.view', 'portfolio.view'], $role->fresh()->permissions->pluck('name')->all());
        $this->assertDatabaseHas('audits', ['event' => 'role_permissions_updated']);
    }
}
