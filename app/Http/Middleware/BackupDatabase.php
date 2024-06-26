<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Spatie\DbDumper\Databases\MySql;
use Illuminate\Support\Facades\Config;

class BackupDatabase
{
    public function handle($request, Closure $next)
    {
        $this->backupIfNeeded();
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
            if ( Carbon::createFromFormat('Y-m-d', $date)->isBefore(Carbon::today()->subDays(3)) ) {
                Storage::delete($file);
            }
        });

        // Создаем новый бэкап
        $this->createBackup($dateToday);
    }

    protected function createBackup($date) {
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
