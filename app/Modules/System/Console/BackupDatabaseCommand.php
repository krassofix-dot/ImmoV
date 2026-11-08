<?php

namespace App\Modules\System\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Datenbank-Sicherung als gzip-komprimierter SQL-Dump nach
 * storage/app/backups. Gedacht für den Aufruf vor jeder Migration
 * (deploy.command) und täglich per Cron.
 */
class BackupDatabaseCommand extends Command
{
    protected $signature = 'immov:backup-db {--keep=30 : Anzahl Sicherungen, die behalten werden} {--label= : Zusatz im Dateinamen, z. B. pre-migrate}';

    protected $description = 'Datenbank sichern (MariaDB/MySQL via mysqldump, SQLite als Dateikopie)';

    public function handle(): int
    {
        $dir = storage_path('app/backups');
        File::ensureDirectoryExists($dir, 0700);

        $label = preg_replace('/[^a-z0-9-]/i', '', (string) $this->option('label'));
        $base = 'db-'.now()->format('Ymd-His').($label !== '' ? "-{$label}" : '');
        $connection = config('database.default');
        $cfg = config("database.connections.{$connection}");

        $result = match ($cfg['driver']) {
            'mysql', 'mariadb' => $this->dumpMysql($cfg, "{$dir}/{$base}.sql.gz"),
            'sqlite' => $this->copySqlite($cfg, "{$dir}/{$base}.sqlite"),
            default => $this->fail("Treiber {$cfg['driver']} wird nicht unterstützt."),
        };

        if ($result === null) {
            return self::FAILURE;
        }

        @chmod($result, 0600);
        $this->info('Sicherung erstellt: '.$result.' ('.number_format(filesize($result) / 1024, 1).' KB)');
        $this->prune($dir, max(1, (int) $this->option('keep')));

        return self::SUCCESS;
    }

    private function dumpMysql(array $cfg, string $target): ?string
    {
        $binary = (new ExecutableFinder)->find('mariadb-dump') ?? (new ExecutableFinder)->find('mysqldump');
        if ($binary === null) {
            $this->error('Weder mariadb-dump noch mysqldump gefunden.');

            return null;
        }

        // Zugangsdaten über eine temporäre Datei, damit das Passwort nicht in der Prozessliste steht.
        $credentials = tempnam(sys_get_temp_dir(), 'immov');
        chmod($credentials, 0600);
        file_put_contents($credentials, sprintf(
            "[client]\nuser=\"%s\"\npassword=\"%s\"\nhost=\"%s\"\nport=%d\n",
            addcslashes((string) $cfg['username'], '"\\'),
            addcslashes((string) $cfg['password'], '"\\'),
            addcslashes((string) $cfg['host'], '"\\'),
            (int) $cfg['port'],
        ));

        try {
            $process = new Process([
                $binary, '--defaults-extra-file='.$credentials,
                '--single-transaction', '--quick', '--routines', '--triggers',
                '--default-character-set=utf8mb4', '--no-tablespaces',
                $cfg['database'],
            ]);
            $process->setTimeout(600);

            $gz = gzopen($target, 'wb6');
            $process->run(function (string $type, string $buffer) use ($gz) {
                if ($type === Process::OUT) {
                    gzwrite($gz, $buffer);
                }
            });
            gzclose($gz);

            if (! $process->isSuccessful()) {
                @unlink($target);
                $this->error('Dump fehlgeschlagen: '.trim($process->getErrorOutput()));

                return null;
            }
        } finally {
            @unlink($credentials);
        }

        return $target;
    }

    private function copySqlite(array $cfg, string $target): ?string
    {
        if (! is_file($cfg['database'])) {
            $this->error('SQLite-Datei nicht gefunden.');

            return null;
        }
        copy($cfg['database'], $target);

        return $target;
    }

    private function prune(string $dir, int $keep): void
    {
        $files = collect(File::files($dir))
            ->filter(fn ($f) => str_starts_with($f->getFilename(), 'db-'))
            ->sortByDesc(fn ($f) => $f->getFilename())
            ->values();

        foreach ($files->slice($keep) as $old) {
            File::delete($old->getPathname());
        }
    }
}
