#!/usr/bin/env bash
# Baut ein Release-Paket für deploy.command (Projekt 7 «ImmoV»).
#
# Ergebnis: dist/ImmoV-release-<datum>-<commit>.zip mit
#   immov-private/   → wird nach ~/immov-private hochgeladen
#   public/          → Inhalt wird nach ~/www/2ndgreen.com/ImmoV hochgeladen
#
# Benötigt PHP 8.3, Composer und Node 20+. Läuft normalerweise in GitHub
# Actions (Workflow «Release-Paket»), lokal nur bei Bedarf.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

STAMP="$(date +%Y%m%d-%H%M)"
SHA="$(git rev-parse --short HEAD 2>/dev/null || echo local)"
NAME="ImmoV-release-${STAMP}-${SHA}"
OUT="$ROOT/dist"
STAGE="$OUT/$NAME"

rm -rf "$STAGE"
mkdir -p "$STAGE/immov-private" "$STAGE/public"

echo "→ Frontend bauen"
npm ci --no-audit --no-fund
npm run build

echo "→ App-Code kopieren"
tar -cf - \
  --exclude='./.git' --exclude='./.github' --exclude='./node_modules' --exclude='./vendor' \
  --exclude='./tests' --exclude='./dist' --exclude='./public' --exclude='./.env' --exclude='./.env.*' \
  --exclude='./database/database.sqlite' --exclude='./storage/app/*' --exclude='./storage/logs/*' \
  --exclude='./storage/framework/cache/data/*' --exclude='./storage/framework/sessions/*' \
  --exclude='./storage/framework/views/*' --exclude='./bootstrap/cache/*.php' \
  --exclude='./phpunit.xml' --exclude='./.phpunit*' --exclude='*.log' \
  . | tar -xf - -C "$STAGE/immov-private"

# Leere Laufzeitordner sicherstellen
for d in storage/app/private storage/app/backups storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache; do
  mkdir -p "$STAGE/immov-private/$d"
done

echo "→ Composer-Abhängigkeiten (ohne Dev-Pakete)"
composer install --working-dir="$STAGE/immov-private" --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress
# Falls Composer Pakete als Git-Klon statt als Archiv geholt hat: Git-Metadaten entfernen
find "$STAGE/immov-private/vendor" -type d -name .git -prune -exec rm -rf {} +

echo "→ Web-Root zusammenstellen"
(cd public && tar -cf - --exclude='./index.php' --exclude='./hot' --exclude='./storage' .) | tar -xf - -C "$STAGE/public"
cp deploy/index.production.php "$STAGE/public/index.php"

echo "$NAME" > "$STAGE/immov-private/RELEASE"

echo "→ ZIP erstellen"
(cd "$STAGE" && zip -qr "$OUT/$NAME.zip" immov-private public)
rm -rf "$STAGE"

echo "Fertig: dist/$NAME.zip"
