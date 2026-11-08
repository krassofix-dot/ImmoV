<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Portfolio-Hierarchie: Land → Liegenschaft → Gebäude → Einheit.
 *
 * Beträge werden als DECIMAL gespeichert (nie FLOAT), immer zusammen mit
 * der Originalwährung.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->char('code', 2)->unique();          // ISO 3166-1 alpha-2
            $table->string('name_de');
            $table->string('name_en');
            $table->char('default_currency', 3);        // ISO 4217
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('street')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('ownership_type', 40)->nullable();
            $table->string('property_type', 40)->nullable();
            $table->unsignedSmallInteger('construction_year')->nullable();
            $table->string('plot_number')->nullable();
            $table->decimal('plot_area_m2', 12, 2)->nullable();
            $table->text('land_registry_info')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->char('purchase_currency', 3)->nullable();
            $table->date('purchase_date')->nullable();
            $table->text('energy_supply')->nullable();
            $table->text('technical_systems')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('construction_year')->nullable();
            $table->unsignedTinyInteger('floors')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('floor', 40)->nullable();
            $table->decimal('area_m2', 10, 2)->nullable();
            $table->decimal('rooms', 4, 1)->nullable();
            $table->string('usage_type', 40)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 20)->default('person');      // person | company
            $table->string('role', 20)->default('tenant');      // tenant | owner | supplier | other
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('street')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('city')->nullable();
            $table->char('country_code', 2)->nullable();
            $table->string('iban', 34)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Nutzung einer Einheit über einen Zeitraum (Miete, Eigennutzung, Leerstand …)
        Schema::create('occupancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->restrictOnDelete();
            $table->foreignId('party_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('type', 30);                         // rental | owner_occupied | private_use | vacant
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('rent_net', 12, 2)->nullable();
            $table->decimal('advance_payment', 12, 2)->nullable(); // Akonto Nebenkosten pro Monat
            $table->char('currency', 3)->nullable();
            $table->string('contract_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['unit_id', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('occupancies');
        Schema::dropIfExists('parties');
        Schema::dropIfExists('units');
        Schema::dropIfExists('buildings');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('countries');
    }
};
