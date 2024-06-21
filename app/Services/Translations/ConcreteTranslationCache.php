<?php

namespace App\Services\Translations;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class ConcreteTranslationCache extends TranslationCache
{
    /**
     * Реализация метода для загрузки переводов по ключу.
     *
     * @param string $key Ключ переводов
     * @return array Массив переводов
     */
    protected function loadTranslations($key): array
    {
        $translations = [];
        $directory = resource_path("lang/{$key}");

        // Проверяем существование каталога с переводами для указанной локали
        if (!is_dir($directory)) {
            Log::warning("Translations directory not found for locale: {$key}");
            return [];
        }

        // Получаем все файлы в каталоге переводов для указанной локали
        $translationFiles = File::files($directory);

        foreach ($translationFiles as $file) {
            // Получаем имя файла без расширения (например, 'menu' из 'menu.php')
            $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            // Загружаем переводы из каждого файла и добавляем в массив
            $translations[$filename] = Lang::get($filename, [], $key);
        }

        return $translations;
    }
}

