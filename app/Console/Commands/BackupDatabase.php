<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Throwable;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     */
    protected $description = 'Backup selected MySQL databases to provision/db_backup (local only)';

    /**
     * Databases to back up (exact names as they exist in MySQL).
     */
    protected array $databases = [
        'helpdesk',
    ];

    public function handle(): int
    {
        /* -----------------------------------------------------------------
         | Environment guard
         |----------------------------------------------------------------- */
        if (!App::environment('local')) {
            $this->error('This command may only be run in the local environment.');
            return \Symfony\Component\Console\Command\Command::FAILURE;
        }

        if (empty($this->databases)) {
            $this->warn('No databases defined for backup.');
            return \Symfony\Component\Console\Command\Command::SUCCESS;
        }

        /* -----------------------------------------------------------------
         | Resolve backup directory
         |----------------------------------------------------------------- */
        $backupDir = base_path('provision/db_backup');

        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        /* -----------------------------------------------------------------
         | Database configuration
         |----------------------------------------------------------------- */
        $connection = Config::get('database.default');
        $config = Config::get("database.connections.$connection");

        if (($config['driver'] ?? null) !== 'mysql') {
            $this->error('This command only supports MySQL connections.');
            return \Symfony\Component\Console\Command\Command::FAILURE;
        }

        $host = (string)($config['host'] ?? '127.0.0.1');
        $port = (string)($config['port'] ?? 3306);
        $username = (string)($config['username'] ?? '');
        $password = (string)($config['password'] ?? '');

        if ($username === '') {
            $this->error('MySQL username is not configured.');
            return \Symfony\Component\Console\Command\Command::FAILURE;
        }

        /* -----------------------------------------------------------------
         | Process each database (raw mysqldump with shell redirection)
         |----------------------------------------------------------------- */
        foreach ($this->databases as $database) {
            $outputFile = $backupDir . DIRECTORY_SEPARATOR . $database . '.sql';

            $this->info("Backing up database [{$database}]…");

            // Exactly equivalent to:
            // mysqldump -h <host> -P <port> -u <user> -p<pass> <db> > <file>
            //
            // NOTE: This is intentionally "raw" and non-interactive; the password
            // is embedded to match the shell command behaviour you requested.
            $command = sprintf(
                'mysqldump -h %s -P %s -u %s -p%s %s > %s',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($outputFile)
            );

            $process = Process::fromShellCommandline($command);

            // Large databases can exceed fixed timeouts; mimic shell behaviour.
            $process->setTimeout(null);

            try {
                $process->mustRun();
                $this->line("→ Saved {$database}.sql");
            } catch (Throwable $e) {
                $this->error("Failed backing up database [{$database}]");

                // Show mysqldump stderr if present (more useful than generic exception)
                $stderr = trim($process->getErrorOutput() ?? '');
                if ($stderr !== '') {
                    $this->line($stderr);
                } else {
                    $this->line($e->getMessage());
                }
            }
        }

        $this->info('Backup process completed.');

        return \Symfony\Component\Console\Command\Command::SUCCESS;
    }
}
