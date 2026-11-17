<?php

use Illuminate\Support\Facades\Schedule;

/*
 * Hostpoint-Cronjob (jede Minute):
 *   php ~/immov-private/artisan schedule:run >> /dev/null 2>&1
 */

// Tägliche Datenbanksicherung, 30 Stück werden behalten.
Schedule::command('immov:backup-db --keep=30')->dailyAt('02:30')->withoutOverlapping();

// Warteschlange ohne Dauerprozess abarbeiten (Hostpoint erlaubt keine Hintergrunddienste).
Schedule::command('queue:work --stop-when-empty --max-time=50')->everyMinute()->withoutOverlapping();
