<?php

/*
 * Einstiegspunkt für Hostpoint (wird vom Release-Build als public/index.php
 * eingesetzt).
 *
 *   ~/www/2ndgreen.com/ImmoV/index.php   ← diese Datei (Web-Root)
 *   ~/immov-private/                      ← App-Code, .env, storage, vendor
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$base = __DIR__.'/../../../immov-private';

if (file_exists($maintenance = $base.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $base.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $base.'/bootstrap/app.php';

// Assets (build/manifest.json) liegen im Web-Root, nicht in immov-private/public.
$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
