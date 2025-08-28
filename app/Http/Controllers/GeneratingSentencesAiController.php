<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GeneratingSentencesAiController extends Controller
{
    /**
     * Генерирует предложения через Google Gemini API
     */
    public function generateSentence(Request $request)
    {
        $words = $request->input('arr_words', []);

        if (empty($words)) {
            Log::error('Empty words array received');
            return response()->json(['sentences' => [], 'error' => 'No words provided'], 400);
        }

        $sentences = [];

        foreach ($words as $word) {
            $word = trim($word);
            if (empty($word)) {
                Log::warning('Empty word skipped', ['word' => $word]);
                continue;
            }

            $responseArray = $this->getSentencesFromGemini($word);

            foreach ($responseArray as $item) {
                // Проверка на ошибки
                if (!isset($item['en']) || !isset($item['ru'])) {
                    $sentences[] = ['original' => $item['en'] ?? 'Error', 'translated' => $item['ru'] ?? ''];
                    continue;
                }

                $enClean = $this->sanitizeText($item['en']);
                $ruClean = $this->sanitizeText($item['ru']);

                $sentences[] = [
                    'original' => $enClean,
                    'translated' => $ruClean,
                ];
            }
        }

        return new ApiResponse(compact('sentences'));
    }

    /**
     * Генерирует 5 простых литературных предложений с указанным словом и их перевод на русский
     * через Google Gemini API.
     *
     * @param string $word Слово, которое должно присутствовать в предложениях.
     * @return array Массив объектов ['en' => английское предложение, 'ru' => перевод на русский].
     *               В случае ошибки возвращается массив с одной записью с ключами 'en' и 'ru', где текст описывает ошибку.
     */
    private function getSentencesFromGemini(string $word): array
    {
        // Получаем ключ Gemini API из .env
        $apiKey = env('GOOGLE_AI_API_KEY');
        if (empty($apiKey)) {
            // Если ключ не задан, возвращаем ошибку
            return [['en' => "Error: API key not configured", 'ru' => "Ошибка: API ключ не настроен"]];
        }

        // URL для запроса к модели Gemini
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent";

        // Промпт для Gemini: 5 предложений на английском + перевод на русский,
        // строго в формате JSON массива объектов с ключами 'en' и 'ru'
        $prompt = "Generate 5 simple literary sentences using the word: {$word}.
For each sentence, also provide the Russian translation.
Return result strictly as JSON array of objects with fields 'en' and 'ru'.";

        // Формируем тело POST запроса
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        try {
            // Отправляем POST запрос с заголовком API ключа
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-goog-api-key' => $apiKey
            ])->post($url, $payload);

            // Если запрос неуспешен, возвращаем ошибку
            if ($response->failed()) {
                return [['en' => "Error: Google API request failed", 'ru' => "Ошибка: запрос к API не удался"]];
            }

            // Декодируем JSON ответ
            $data = $response->json();
            $generatedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // Убираем обёртку ```json ... ```
            $generatedText = preg_replace('/^```json\s*|\s*```$/', '', trim($generatedText));

            // Преобразуем текст в массив PHP
            $parsed = json_decode($generatedText, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return [['en' => "Error: Invalid JSON from Gemini", 'ru' => "Ошибка: некорректный JSON от Gemini"]];
            }

            // Возвращаем массив предложений с переводом
            return $parsed;

        } catch (\Exception $e) {
            // Ловим исключения и возвращаем в виде ошибки
            return [['en' => "Error: {$e->getMessage()}", 'ru' => "Ошибка: {$e->getMessage()}"]];
        }
    }

    /**
     * Обрабатывает текст, очищает от markdown и нормализует пробелы.
     */
    private function sanitizeText(string $text): string
    {
        // Убираем markdown (** , *, __, _, `)
        $text = preg_replace('/\*\*\s*(.*?)\s*\*\*/s', '$1', $text);
        $text = preg_replace('/\*\s*(.*?)\s*\*/s', '$1', $text);
        $text = preg_replace('/__\s*(.*?)\s*__/s', '$1', $text);
        $text = preg_replace('/_\s*(.*?)\s*_/', '$1', $text);
        $text = preg_replace('/`(.*?)`/s', '$1', $text);
        $text = str_replace('*', '', $text);

        // Удаляем управляющие символы
        $text = preg_replace('/[\x00-\x1F\x7F]/u', '', $text);

        // Нормализуем пробелы
        $text = preg_replace('/\s+/u', ' ', $text);

        // Убираем пробел перед знаками пунктуации
        $text = preg_replace('/\s+([.,!?;:])/u', '$1', $text);

        $text = trim($text);

        return $text;
    }

}
