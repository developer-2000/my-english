<?php

namespace App\Http\Controllers;

use App\Http\Requests\AIStudio\GenerateSentencesRequest;
use App\Http\Responses\ApiResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;

class GeneratingSentencesAiController extends Controller
{
    /**
     * Генерирует предложения
     *
     * @param  Request  $request
     *
     * @throws GuzzleException
     */
    public function generateSentence(GenerateSentencesRequest $request): ApiResponse
    {
        // масив слов которые будут в предложениях
        $words = $request->arr_words;
        $sentences = [];

        foreach ($words as $word) {
            $response = $this->getSentencesAIStudio($word);
            $individualSentences = $this->processResponse($response, $word);

            foreach ($individualSentences as $sentence) {
                if (strpos($sentence, 'Error') === false && strpos($sentence, 'Failed') === false) {
                    // Переводим предложение на русский язык
                    $translatedSentence = $this->translateToRussian($sentence);
                    // Добавляем в массив результатов
                    $sentences[] = [
                        'original' => $sentence,
                        'translated' => $translatedSentence,
                    ];
                }
            }
        }

        return new ApiResponse(compact('sentences'));
    }

    /**
     * Запрос на AI21 Studio и получение ответа
     *
     * @throws GuzzleException
     */
    private function getSentencesAIStudio($word): string
    {
        $client = new Client;
        // Поместите ваш API ключ в файл .env
        $apiKey = env('STUDIO_AI_API_KEY');
        $prompt = 'Create five simple literary sentences that are not similar to each other in English and separate them with a vertical symbol | using the given word: '.$word;

        try {
            // Используем правильный URL-адрес для AI21 Studio API
            $response = $client->post('https://api.ai21.com/studio/v1/j2-ultra/complete', [
                'headers' => [
                    'Authorization' => 'Bearer '.$apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'prompt' => $prompt,
                    'numResults' => 1,
                    'maxTokens' => 100, // Ограничиваем количество токенов
                    'temperature' => 0.7,
                    'topP' => 0.9,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            // Убираем лишние пробелы и переносы строк
            return trim($data['completions'][0]['data']['text']) ?? 'Failed to generate sentence.';
        } catch (\Exception $e) {
            // Обрабатываем ошибки
            return 'Error: '.$e->getMessage();
        }
    }

    /**
     * Обрабатывает ответ API и извлекает предложения
     */
    private function processResponse(string $response, string $word): array
    {
        $sentencesArray = explode('.', $response);

        // Фильтруем массив, оставляем только предложения, содержащие ключевое слово
        $filteredSentences = array_filter($sentencesArray, function ($sentence) use ($word) {
            return stripos($sentence, $word) !== false;
        });

        // Удаляем символы '|'
        $filteredSentences = array_map(function ($sentence) {
            return str_replace('|', '', $sentence);
        }, $filteredSentences);

        // Удаляем лишние пробелы (больше 1) и пробелы в начале и конце строки
        // Добавляем точку в конце предложения, если её нет
        return array_map(function ($sentence) {
            $cleanedSentence = preg_replace('/\s{2,}/', ' ', trim($sentence));
            if (substr($cleanedSentence, -1) !== '.') {
                $cleanedSentence .= '.';
            }

            return $cleanedSentence;
        }, $filteredSentences);
    }

    /**
     * Переводит текст на русский язык
     */
    private function translateToRussian(string $text): string
    {
        // Инициализируем объект для перевода
        $translator = new GoogleTranslate;
        // Устанавливаем исходный и целевой языки
        $translator->setSource('en'); // Исходный язык - английский
        $translator->setTarget('ru'); // Целевой язык - русский

        // Переводим текст
        return $translator->translate($text);
    }
}
