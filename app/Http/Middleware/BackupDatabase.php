<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Spatie\DbDumper\Databases\MySql;
use Spatie\DbDumper\Exceptions\CannotStartDump;
use Spatie\DbDumper\Exceptions\DumpFailed;

class BackupDatabase
{
    public function handle($request, Closure $next)
    {
        // Очищаем любой возможный output buffer
        if (ob_get_level()) {
            ob_clean();
        }

        try {
            \Log::info('Backup middleware triggered');
            $this->backupIfNeeded();
        } catch (\Exception $e) {
            // Логируем ошибку, но не прерываем выполнение
            \Log::error('Backup error: '.$e->getMessage());
            \Log::error('Backup error trace: '.$e->getTraceAsString());
        }

        return $next($request);
    }

    private function backupIfNeeded()
    {
        $dateToday = Carbon::today()->toDateString();
        $backupFiles = collect(Storage::files('backups'));

        // Проверка файла с сегодняшней датой
        $recentBackups = $backupFiles->filter(function ($file) use ($dateToday) {
            return strpos($file, "{$dateToday}.sql") !== false;
        });
        
        // Проверяем есть ли валидный бэкап за сегодня (не пустой файл)
        $validBackupExists = false;
        if ($recentBackups->isNotEmpty()) {
            foreach ($recentBackups as $backupFile) {
                $fileSize = Storage::size($backupFile);
                if ($fileSize > 100) { // Считаем валидным если файл больше 100 байт
                    $validBackupExists = true;
                    break;
                }
            }
        }
        
        // Выход, если есть валидный бэкап за сегодня
        if ($validBackupExists) {
            \Log::info("Valid backup for today already exists");
            return;
        }

        // Удаляем пустые или невалидные бэкапы за сегодня
        $recentBackups->each(function ($file) {
            $fileSize = Storage::size($file);
            if ($fileSize <= 100) {
                Storage::delete($file);
                \Log::info("Deleted invalid backup: {$file} (size: {$fileSize} bytes)");
            }
        });

        // Удаляем бэкапы, которые старше 3 дней
        $backupFiles->each(function ($file) {
            $date = basename($file, '.sql');
            if (Carbon::createFromFormat('Y-m-d', $date)->isBefore(Carbon::today()->subDays(3))) {
                Storage::delete($file);
            }
        });

        // Создаем новый бэкап
        $this->createBackup($dateToday);
    }

    /**
     * Адрес бекапов - \storage\app\backups\2025-02-02.sql
     *
     * @return void
     *
     * @throws CannotStartDump
     * @throws DumpFailed
     */
    private function createBackup($date)
    {
        try {
            // Используем простой PDO бэкап напрямую
            $this->createSimpleBackup($date);
        } catch (\Exception $e) {
            \Log::error("Backup creation failed: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function createSimpleBackup($date)
    {
        try {
            // Простой бэкап через PDO
            $pdo = new \PDO(
                "mysql:host=127.0.0.1;port=3306;dbname=english",
                "root",
                "",
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
            
            $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
            $backupContent = "-- Backup created on " . date('Y-m-d H:i:s') . "\n\n";
            
            foreach ($tables as $table) {
                $backupContent .= "-- Table structure for table `{$table}`\n";
                $createTable = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch();
                $backupContent .= $createTable[1] . ";\n\n";
                
                $backupContent .= "-- Dumping data for table `{$table}`\n";
                $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
                
                if (!empty($rows)) {
                    $columns = array_keys($rows[0]);
                    $backupContent .= "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES\n";
                    
                    $values = [];
                    foreach ($rows as $row) {
                        $rowValues = [];
                        foreach ($row as $value) {
                            $rowValues[] = $pdo->quote($value);
                        }
                        $values[] = "(" . implode(', ', $rowValues) . ")";
                    }
                    $backupContent .= implode(",\n", $values) . ";\n\n";
                }
            }
            
            file_put_contents(storage_path("app/backups/{$date}.sql"), $backupContent);
            \Log::info("Simple backup created successfully: {$date}.sql");
            
        } catch (\Exception $e) {
            \Log::error("Simple backup failed: " . $e->getMessage());
            throw $e;
        }
    }
}
