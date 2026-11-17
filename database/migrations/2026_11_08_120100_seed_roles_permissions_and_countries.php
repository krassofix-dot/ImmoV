<?php

use App\Modules\Users\Permissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Stammdaten, die jede Installation braucht: Rechte, Standardrollen,
 * Länder CH/ES/PT. Idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        Permissions::sync();

        $now = now();
        foreach ([
            ['CH', 'Schweiz', 'Switzerland', 'CHF', 10],
            ['ES', 'Spanien', 'Spain', 'EUR', 20],
            ['PT', 'Portugal', 'Portugal', 'EUR', 30],
        ] as [$code, $de, $en, $cur, $sort]) {
            DB::table('countries')->updateOrInsert(
                ['code' => $code],
                ['name_de' => $de, 'name_en' => $en, 'default_currency' => $cur,
                    'is_active' => true, 'sort_order' => $sort, 'created_at' => $now, 'updated_at' => $now],
            );
        }
    }

    public function down(): void
    {
        // Stammdaten bleiben bestehen.
    }
};
