<?php

namespace App\Services\Translations;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

abstract class TranslationCache
{
    protected $keyPrefix = 'translations:';
    protected $cacheTimeMinutes = 60; // Время кеширования в минутах

    /**
     * Получить переводы по ключу из кеша или загрузить их, если не найдены.
     *
     * @param string $key Ключ переводов
     * @return array Массив переводов
     */
    public function getTranslations(string $key): array
    {
        $cacheKey = $this->keyPrefix . $key;

        // Попробовать сначала получить переводы из кеша
        $translations = Cache::get($cacheKey);

        if (!$translations) {
            // Если переводы не найдены в кеше, загружаем их
            $translations = $this->loadTranslations($key);
            Log::warning("Generate");
            // Если переводы не найдены, логируем это
            if (empty($translations)) {
                Log::warning("Translations not found for key: {$key}");
                return [];
            }

            // Сохраняем переводы в кеш
            Cache::put($cacheKey, $translations, $this->cacheTimeMinutes);
        }
        else{
            Log::warning("Cash");
        }

        return $translations;
    }

    /**
     * Абстрактный метод для загрузки переводов по ключу.
     *
     * @param string $key Ключ переводов
     * @return array Массив переводов
     */
    abstract protected function loadTranslations($key);

    /**
     * Очистить переводы по ключу из кеша.
     *
     * @param string $key Ключ переводов
     * @return void
     */
    public function clearTranslations(string $key)
    {
        $cacheKey = $this->keyPrefix . $key;

        // Удаляем переводы из кеша
        Cache::forget($cacheKey);
    }

    /**
     * Очистить все переводы из кеша.
     *
     * @return void
     */
    public function clearAllTranslations()
    {
        // Используем паттерн удаления по ключу для удаления всех переводов из кеша
        Cache::forget($this->keyPrefix . '*');
    }
}

