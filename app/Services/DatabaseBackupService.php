<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DatabaseBackupService
{
    public function backup(): string
    {
        $connection = config('database.default');
        $dbConfig   = config("database.connections.$connection");
        $database   = $dbConfig['database'];

        // ambil semua tabel
        $tables = DB::select('SHOW TABLES');
        $key = "Tables_in_{$database}";

        $sqlScript = "-- Backup Database: {$database}\n";
        $sqlScript .= "-- Waktu: " . date('Y-m-d H:i:s') . "\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$key;

            // Struktur tabel
            $createTable = DB::select("SHOW CREATE TABLE `$tableName`")[0]->{'Create Table'};
            $sqlScript .= "\n\nDROP TABLE IF EXISTS `$tableName`;\n";
            $sqlScript .= $createTable . ";\n\n";

            // Data tabel
            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $values = array_map(function ($value) {
                    return isset($value) ? "'" . addslashes($value) . "'" : "NULL";
                }, (array) $row);

                $sqlScript .= "INSERT INTO `$tableName` VALUES(" . implode(',', $values) . ");\n";
            }

            $sqlScript .= "\n\n";
        }
        $fileName = "backup_". $database ."_" . date('Ymd_His') . ".sql";
      Storage::disk('public')->put("backups/$fileName", $sqlScript);
     return storage_path("backups/".$fileName );
   }
}
