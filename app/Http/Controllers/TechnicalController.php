<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\EnWord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TechnicalController extends Controller
{
    /**
     * Показать техническую страницу
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('index');
    }

    /**
     * Записать все правильные глаголы в JSON файл
     *
     * @return ApiResponse
     */
    public function getRegularVerbs(): ApiResponse
    {
        $regularVerbs = EnWord::where('type_id', 1)
            ->get();

        // Создаем массив только с полями модели EnWord
        $filteredVerbs = $regularVerbs->map(function ($verb) {
            return [
                'id' => $verb->id,
                'word' => $verb->word,
                'translation' => $verb->translation,
                'url_image' => $verb->url_image,
                'description' => $verb->description,
                'type_id' => $verb->type_id,
                'time_forms' => $verb->time_forms,
                'is_known' => $verb->is_known,
                'unknown_order' => $verb->unknown_order,
                'created_at' => $verb->created_at,
                'updated_at' => $verb->updated_at,
            ];
        });

        // Создаем JSON файл в storage/app/json words
        $jsonContent = json_encode($filteredVerbs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $date = date('Y-m-d');
        $filename = 'but-regular-verbs-' . $date . '.json';
        $filepath = storage_path('app/json words/' . $filename);

        // Создаем директорию если не существует
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        // Записываем файл
        $result = file_put_contents($filepath, $jsonContent);

        return new ApiResponse([
            'message' => 'Файл ' . $filename . ' успешно создан в storage/app/json words/',
            'file_name' => $filename
        ]);
    }

    /**
     * Записать все неправильные глаголы в JSON файл
     *
     * @return ApiResponse
     */
    public function getIrregularVerbs(): ApiResponse
    {
        $irregularVerbs = EnWord::where('type_id', 4)
            ->get();

        // Создаем массив только с полями модели EnWord
        $filteredVerbs = $irregularVerbs->map(function ($verb) {
            return [
                'id' => $verb->id,
                'word' => $verb->word,
                'translation' => $verb->translation,
                'url_image' => $verb->url_image,
                'description' => $verb->description,
                'type_id' => $verb->type_id,
                'time_forms' => $verb->time_forms,
                'is_known' => $verb->is_known,
                'unknown_order' => $verb->unknown_order,
                'created_at' => $verb->created_at,
                'updated_at' => $verb->updated_at,
            ];
        });

        // Создаем JSON файл в storage/app/json words
        $jsonContent = json_encode($filteredVerbs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $date = date('Y-m-d');
        $filename = 'but-irregular-verbs-' . $date . '.json';
        $filepath = storage_path('app/json words/' . $filename);

        // Создаем директорию если не существует
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        // Записываем файл
        $result = file_put_contents($filepath, $jsonContent);

        return new ApiResponse([
            'message' => 'Файл ' . $filename . ' успешно создан в storage/app/json words/',
            'file_name' => $filename
        ]);
    }

    /**
     * Сохранить правильные глаголы в базу данных
     *
     * @return ApiResponse
     */
    public function saveRegularVerbsInDatabase(): ApiResponse
    {
        try {
            // Получаем сегодняшнюю дату в формате Y-m-d
            $date = date('Y-m-d');
            $filename = 'but-regular-verbs-' . $date . '.json';

            // 1 Загружаем данные из JSON файла
            $result = $this->loadVerbsFromJsonFile($filename);
            // Error
            if (!$result['status']) {
                return new ApiResponse([ 'message' => $result['message'] ], true);
            }

            $verbs = $result['data'];

            // Начинаем транзакцию
            DB::beginTransaction();

            // 2 Удаляем все записи с type_id = 1 (правильные глаголы)
            EnWord::where('type_id', 1)->delete();

            // Сохраняем глаголы в базу
            $result = $this->saveVerbsToDatabase($verbs);
            if (!$result['status']) {
                throw new \Exception($result['message']);
            }

            // Подтверждаем транзакцию
            DB::commit();

            return new ApiResponse([
                'message' => 'Правильные глаголы успешно сохранены в базу',
            ]);

        } catch (\Exception $e) {
            // Откатываем транзакцию при ошибке
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return new ApiResponse([
                'message' => 'Ошибка при сохранении правильных глаголов: ' . $e->getMessage()
            ], true);
        }
    }

    /**
     * Сохранить неправильные глаголы в базу данных
     *
     * @return ApiResponse
     */
    public function saveIrregularVerbsInDatabase(): ApiResponse
    {
        try {
            // Получаем сегодняшнюю дату в формате Y-m-d
            $date = date('Y-m-d');
            $filename = 'but-irregular-verbs-' . $date . '.json';

            // 1 Загружаем данные из JSON файла
            $result = $this->loadVerbsFromJsonFile($filename);
            // Error
            if (!$result['status']) {
                return new ApiResponse([ 'message' => $result['message'] ], true);
            }

            $verbs = $result['data'];

            // Начинаем транзакцию
            DB::beginTransaction();

            // 2 Удаляем все записи с type_id = 4 (неправильные глаголы)
            EnWord::where('type_id', 4)->delete();

            // Сохраняем глаголы в базу
            $result = $this->saveVerbsToDatabase($verbs);
            if (!$result['status']) {
                throw new \Exception($result['message']);
            }

            // Подтверждаем транзакцию
            DB::commit();

            return new ApiResponse([
                'message' => 'Неправильные глаголы успешно сохранены в базу',
            ]);

        } catch (\Exception $e) {
            // Откатываем транзакцию при ошибке
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return new ApiResponse([
                'message' => 'Ошибка при сохранении неправильных глаголов: ' . $e->getMessage()
            ], true);
        }
    }

    /**
     * Выбрать JSON файл по адресу
     *
     * @param string $filename
     * @return array|ApiResponse
     */
    private function loadVerbsFromJsonFile(string $filename)
    {
        $filepath = storage_path('app/json words/' . $filename);

        // 1 Проверяем существование файла
        if (!file_exists($filepath)) {
            return [
                'status' => false,
                'message' => 'Файл ' . $filename . ' не найден',
                'data' => null
            ];
        }

        // 2 Читаем JSON файл
        $jsonContent = file_get_contents($filepath);
        $verbs = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => false,
                'message' => 'Ошибка при парсинге JSON файла: ' . json_last_error_msg(),
                'data' => null
            ];
        }

        return [
            'status' => true,
            'message' => null,
            'data' => $verbs
        ];
    }

    /**
     * Сохранить глаголы в базу данных
     *
     * @param array $verbs
     * @return array
     */
    private function saveVerbsToDatabase(array $verbs): array
    {
        try {
            // Создаем массив объектов для сохранения
            $wordsToSave = [];
            foreach ($verbs as $verbData) {
                $wordsToSave[] = [
                    'id' => $verbData['id'],
                    'word' => $verbData['word'],
                    'translation' => $verbData['translation'],
                    'url_image' => $verbData['url_image'],
                    'description' => $verbData['description'],
                    'type_id' => $verbData['type_id'],
                    'time_forms' => $verbData['time_forms'],
                    'is_known' => $verbData['is_known'],
                    'unknown_order' => $verbData['unknown_order'],
                    'created_at' => $verbData['created_at'],
                    'updated_at' => $verbData['updated_at']
                ];
            }

            // Сохраняем объекты в базу с сохранением ID
            foreach ($wordsToSave as $wordData) {
                EnWord::create($wordData);
            }

            return [
                'status' => true,
                'message' => null,
            ];

        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Ошибка при сохранении глаголов: ' . $e->getMessage(),
            ];
        }
    }
}
