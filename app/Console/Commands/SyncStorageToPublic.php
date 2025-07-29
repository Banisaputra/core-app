<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncStorageToPublic extends Command
{
    protected $signature = 'sync:storage-to-public';
    protected $description = 'Copy files from storage to public directory';

    public function handle()
    {
        $source = storage_path('app/public');
        $destination = public_path('storage');

        // Buat direktori jika belum ada
        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        // Kosongkan direktori tujuan (opsional)
        File::cleanDirectory($destination);

        // Copy file rekursif
        File::copyDirectory($source, $destination);

        $this->info('Files copied successfully from storage to public directory');
        
        return Command::SUCCESS;
    }
}