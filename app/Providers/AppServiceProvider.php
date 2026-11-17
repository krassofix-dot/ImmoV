<?php

namespace App\Providers;

use App\Models\User;
use App\Modules\Parties\Models\Occupancy;
use App\Modules\Parties\Models\Party;
use App\Modules\Portfolio\Models\Building;
use App\Modules\Portfolio\Models\Country;
use App\Modules\Portfolio\Models\Property;
use App\Modules\Portfolio\Models\Unit;
use App\Modules\Users\Listeners\RecordAuthenticationEvents;
use App\Modules\Users\Permissions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Stabile Kurznamen statt Klassennamen in audits und model_has_roles,
        // damit Klassen später verschoben werden können.
        Relation::morphMap([
            'user' => User::class,
            'country' => Country::class,
            'property' => Property::class,
            'building' => Building::class,
            'unit' => Unit::class,
            'party' => Party::class,
            'occupancy' => Occupancy::class,
        ]);

        Model::preventLazyLoading(! $this->app->isProduction());

        Gate::before(fn (User $user) => $user->hasRole(Permissions::ADMIN_ROLE) ? true : null);

        Event::subscribe(RecordAuthenticationEvents::class);

        Password::defaults(fn () => Password::min(10)->letters()->numbers());

        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }
    }
}
