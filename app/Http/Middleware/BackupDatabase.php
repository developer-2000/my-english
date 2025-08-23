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
            $this->backupIfNeeded();
        } catch (\Exception $e) {
            // Логируем ошибку, но не прерываем выполнение
            \Log::error('Backup error: '.$e->getMessage());
        }

        return $next($request);
    }

    protected function backupIfNeeded()
    {
        $dateToday = Carbon::today()->toDateString();
        $backupFiles = collect(Storage::files('backups'));

        // Проверка файла с сегодняшней датой
        $recentBackups = $backupFiles->filter(function ($file) use ($dateToday) {
            return strpos($file, "{$dateToday}.sql") !== false;
        });
        // Выход, если есть бэкап за сегодня
        if ($recentBackups->isNotEmpty()) {
            return;
        }

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
    protected function createBackup($date)
    {
        // Получаем настройки базы данных из конфигурации Laravel
        $dbName = Config::get('database.connections.mysql.database');
        $dbUser = Config::get('database.connections.mysql.username');
        $dbPass = Config::get('database.connections.mysql.password');

        MySql::create()
            ->setDbName($dbName)
            ->setUserName($dbUser)
            ->setPassword($dbPass)
            ->dumpToFile(storage_path("app/backups/{$date}.sql"));
    }
}
