<?php 

// app/Console/Commands/DatabaseBackup.php
namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup {--download}';
    protected $description = 'Backup database MySQL';

    public function handle()
    {
        $db   = config('database.connections.mysql.database');
        $user = config('database.connections.mysql.username');
        $pass = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        $fileName = $db . '_' . Carbon::now()->format('Ymd_His') . '.sql';
        $filePath = storage_path('app/backups/' . $fileName);

        if (!is_dir(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        $command = "mysqldump -h {$host} -u {$user} " . (!empty($pass) ? "-p{$pass} " : "") . "{$db} > {$filePath}";
        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $this->info("Backup berhasil: {$filePath}");

            // kalau dari controller dipanggil pakai opsi --download
            if ($this->option('download')) {
                 cache(['last_backup_file' => $filePath], now()->addMinutes(5));
            }
        } else {
            $this->error("Backup gagal. Cek konfigurasi atau permission.");
        }
    }
}

?>

