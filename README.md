# ImmoV – Immobilienverwaltung

Webbasierte Verwaltung der privaten Liegenschaften in der Schweiz, Spanien und Portugal.
Laravel 12 · PHP 8.3 · MariaDB · Blade + Tailwind (lokal/CI gebaut, kein Node im Betrieb).

Architektur und Analyse: Phase-1-Dokument (Claude Doc «Immobilienverwaltung – Phase 1»).

## Stand: Phase 2 – Grundsystem

| Modul | Inhalt |
|---|---|
| Benutzer & Rechte | Login (Fortify), keine Selbstregistrierung, Rollen Administrator / Verwalter / Mitarbeiter, granulare Rechte je Modul (Matrix unter *Rollen & Rechte*), Konten aktivieren/deaktivieren, Schutz vor Aussperren des letzten Administrators, Passwort-Reset per E-Mail, Zwei-Faktor-Anmeldung (TOTP, freiwillig, vom Admin zurücksetzbar) |
| Änderungsprotokoll | Jede Änderung an Stammdaten (alt → neu), Anmeldungen, fehlgeschlagene Anmeldungen, Rollen- und 2FA-Änderungen. Keine Passwörter oder 2FA-Geheimnisse im Protokoll. |
| Portfolio | Land → Liegenschaft → Gebäude → Einheit, Stammdaten laut Master-Prompt (Adresse, Eigentumsverhältnis, Gebäudeart, Baujahr, Grundstück, Kaufpreis mit Währung, Energie, Technik, Bemerkungen) |
| Parteien | Personen und Firmen (Mieter, Eigentümer, Lieferanten), Nutzungen je Einheit mit Zeitraum (Vermietung, Eigennutzung, private Feriennutzung, Leerstand), Nettomiete und Akonto mit Währung |
| Übersicht | Kennzahlen gefiltert nach Land / Liegenschaft / Gebäude / Einheit, Nutzung heute, vertragliche Monatswerte je Währung, letzte Änderungen. Finanzkennzahlen sind als «folgt» markiert (Phase 3). |
| System | DE/EN je Benutzer, Länderverwaltung, `immov:backup-db`, `immov:create-admin`, `immov:seed-portfolio` |

Bewusst **nicht** enthalten: Buchungen, Abrechnungslogik, Dokumente, Importe (Phasen 3 ff.).

Beträge werden als `DECIMAL` gespeichert und ohne Gleitkomma verarbeitet (brick/math).

## Module im Code

```
app/Modules/
  Dashboard/   Übersicht
  Portfolio/   Länder, Liegenschaften, Gebäude, Einheiten (Models, Enums, Controller)
  Parties/     Parteien, Nutzungen
  Users/       Benutzer, Rollen, Profil, Rechteliste (Permissions.php), Anmelde-Protokoll
  System/      Änderungsprotokoll, Artisan-Befehle, AuditLogger
```

Neue Rechte werden in `app/Modules/Users/Permissions.php` ergänzt und per Migration mit `Permissions::sync()` eingespielt.

## Deployment auf Hostpoint (deploy.command, Projekt 7)

### Release-Paket holen

1. GitHub → **Actions** → **Release-Paket** → **Run workflow** (Branch `main`).
2. Nach ca. 3 Minuten das Artefakt **ImmoV-release** herunterladen. GitHub liefert es als ZIP; darin liegt `ImmoV-release-<datum>-<commit>.zip`.
3. Diese innere ZIP-Datei nach `~/Downloads` legen und `deploy.command` → Projekt 7 starten.

Das Paket enthält:

```
immov-private/   → ~/immov-private          (App, vendor, ohne .env)
public/          → ~/www/2ndgreen.com/ImmoV (index.php zeigt auf ../../../immov-private)
```

### Erstinstallation (einmalig, per SSH)

```sh
cd ~/immov-private
cp deploy/env.production.example .env
nano .env                         # DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD, MAIL_* ausfüllen
php artisan key:generate
php artisan migrate --force
php artisan immov:create-admin deine@email.ch --name="Daniel"
php artisan immov:seed-portfolio  # optional: die fünf Liegenschaften als Grundgerüst
```

Datenbank vorher im Hostpoint Control Panel anlegen (MariaDB, Zeichensatz utf8mb4).

### Cronjob (Hostpoint Control Panel → Cronjobs, jede Minute)

```
php ~/immov-private/artisan schedule:run >> /dev/null 2>&1
```

Damit laufen die tägliche Datenbanksicherung (02:30, 30 Stück) und die Warteschlange.

### Bei jedem Update

`deploy.command` führt nach Bestätigung Wartungsmodus → `migrate --force` → Caches aus.
**Empfohlen:** vor `migrate` eine Sicherung ziehen. Zeile für das Script (vor dem Migrate-Aufruf):

```sh
ssh hostpoint 'cd ~/immov-private && php artisan immov:backup-db --label=pre-migrate'
```

Sicherungen liegen in `~/immov-private/storage/app/backups` (nur für den Benutzer lesbar).

### Hinweise

- Die Übersicht liegt unter `/ImmoV/dashboard`. `public/.htaccess` leitet `/ImmoV/` dorthin weiter; das umgeht einen Laravel-Fehler mit `route:cache` in Unterordnern.
- `.htaccess` erzwingt HTTPS und setzt `noindex`.
- Passwörter: Argon2id (`HASH_DRIVER=argon2id`), mindestens 10 Zeichen mit Buchstaben und Ziffern.

## Entwicklung

```sh
composer install && npm ci
cp .env.example .env && php artisan key:generate
touch database/database.sqlite && php artisan migrate
php artisan immov:create-admin admin@example.com --name=Admin
php artisan immov:seed-portfolio
npm run build && php artisan serve
php artisan test          # SQLite; CI testet zusätzlich gegen MariaDB 10.11
vendor/bin/pint           # Code-Stil
```
