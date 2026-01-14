<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class RestoreDatabase extends Command
{
    protected $signature = 'db:restore
        {--dry-run : Show what would be executed without making changes}
        {--fresh : Drop and recreate each database before restoring}';

    protected $description = 'Restore MySQL databases from provision/db_backup (local only). DB name is derived from filename (uppercased).';

    public function handle(): int
    {

        if (!App::environment('local')) {
            $this->error('This command may only be run in the local environment.');
            return \Symfony\Component\Console\Command\Command::FAILURE;
        }

        $dryRun = (bool)$this->option('dry-run');
        $fresh = (bool)$this->option('fresh');

        $backupDir = base_path('provision/db_backup');

        if (!File::exists($backupDir)) {
            $this->error("Backup directory not found: {$backupDir}");
            return \Symfony\Component\Console\Command\Command::FAILURE;
        }

        $files = collect(File::files($backupDir))
            ->filter(fn($f) => strtolower($f->getExtension()) === 'sql')
            ->sortBy(fn($f) => $f->getFilename())
            ->values();

        if ($files->isEmpty()) {
            $this->warn("No .sql files found in: {$backupDir}");
            return \Symfony\Component\Console\Command\Command::SUCCESS;
        }

        $connection = Config::get('database.default');
        $config = Config::get("database.connections.$connection");

        if (($config['driver'] ?? null) !== 'mysql') {
            $this->error('This command only supports MySQL connections.');
            return \Symfony\Component\Console\Command\Command::FAILURE;
        }

        $host = (string)($config['host'] ?? '127.0.0.1');
        $port = (int)($config['port'] ?? 3306);
        $username = (string)($config['username'] ?? 'root');
        $password = (string)($config['password'] ?? '');

        // Base mysql client args (no password on argv)
        $mysqlBaseArgs = [
            'mysql',
            "--host={$host}",
            "--port={$port}",
            "--user={$username}",
            '--default-character-set=utf8mb4',
            '--binary-mode',
            '--show-warnings',
        ];

        // Prefer env over argv for password (still local-only; reduces accidental leakage)
        $mysqlEnv = array_merge($_ENV, $_SERVER, [
            'MYSQL_PWD' => $password,
        ]);

        // Helper: run mysql -e "SQL"
        $runSql = function (string $sql) use ($mysqlBaseArgs, $mysqlEnv, $dryRun): void {
            $cmd = array_merge($mysqlBaseArgs, ['-e', $sql]);

            if ($dryRun) {
                $this->line('[dry-run] ' . $this->formatCmd($cmd));
                return;
            }

            $p = new Process($cmd, null, $mysqlEnv);
            $p->setTimeout(120);
            $p->run();

            if (!$p->isSuccessful()) {
                throw new ProcessFailedException($p);
            }
        };

        foreach ($files as $file) {
            $path = $file->getRealPath();
            $base = $file->getBasename(); // e.g. AAMEDIUK.sql
            $db = strtoupper(pathinfo($base, PATHINFO_FILENAME)); // force uppercase

            if (empty($db)) {
                $this->error("Could not determine database name from filename: {$base}");
                continue;
            }

            if (!$path || !is_file($path)) {
                $this->error("SQL file missing/unreadable: {$base}");
                continue;
            }

            $this->info("Processing [{$base}] → database [{$db}]");

            try {
                if ($fresh) {
                    $this->line($dryRun
                        ? "[dry-run] Would drop and recreate database: {$db}"
                        : "Dropping and recreating database: {$db}"
                    );

                    $runSql("DROP DATABASE IF EXISTS `{$db}`;");
                    $runSql("CREATE DATABASE `{$db}`;");
                } else {
                    $this->line($dryRun
                        ? "[dry-run] Would ensure database exists: {$db}"
                        : "Ensuring database exists: {$db}"
                    );

                    $runSql("CREATE DATABASE IF NOT EXISTS `{$db}`;");
                }

                // Restore by streaming file contents into mysql stdin (no full file in memory)
                $restoreCmd = array_merge($mysqlBaseArgs, [
                    // Make restore more resilient for big dumps:
                    '--max-allowed-packet=1G',
                    '--net-buffer-length=1M',
                    $db,
                ]);

                if ($dryRun) {
                    $this->line('[dry-run] ' . $this->formatCmd($restoreCmd) . " < {$base}");
                    continue;
                }

                $this->line("Restoring into {$db}…");

                $fh = fopen($path, 'rb');
                if ($fh === false) {
                    throw new RuntimeException("Unable to open SQL file for reading: {$path}");
                }

                try {
                    $restore = new Process($restoreCmd, null, $mysqlEnv);

                    // Large restores can take a long time; remove timeout entirely.
                    $restore->setTimeout(null);

                    $restore->setInput($fh);

                    // Optional: stream mysql output to console to help debug failures without buffering everything.
                    $restore->run(function ($type, $buffer) {
                        $out = trim($buffer);
                        if ($out === '') {
                            return;
                        }

                        // mysql warnings/errors typically go to STDERR; show both.
                        if ($type === Process::ERR) {
                            $this->line("<fg=yellow>{$out}</>");
                        } else {
                            $this->line($out);
                        }
                    });

                    if (!$restore->isSuccessful()) {
                        throw new ProcessFailedException($restore);
                    }
                } finally {
                    fclose($fh);
                }

                $this->line("→ Restored {$db}");
            } catch (Throwable $e) {
                $this->error("Failed for [{$db}] from [{$base}]");
                $this->line($e->getMessage());
                continue;
            }
        }

        $this->info($dryRun ? 'Dry-run completed.' : 'Restore process completed.');
        return \Symfony\Component\Console\Command\Command::SUCCESS;
    }

    /**
     * Format a process command for console output (minimal escaping).
     */
    private function formatCmd(array $cmd): string
    {
        return implode(' ', array_map(function ($part) {
            // crude but readable
            return preg_match('/\s/', $part) ? '"' . str_replace('"', '\"', $part) . '"' : $part;
        }, $cmd));
    }
}
