<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AIStudio\GenerateSentencesRequest;
use App\Http\Responses\ApiResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;

class GeneratingSentencesAiController extends Controller
{

    /**
     * Генерирует предложения
     * @param Request $request
     * @return ApiResponse
     * @throws GuzzleException
     */
    public function generateSentence(GenerateSentencesRequest $request): ApiResponse
    {
        // масив слов которые будут в предложениях
        $words = $request->arr_words;
        $sentences = [];

        foreach ($words as $word) {
            $response = $this->getSentencesAIStudio($word);
            $individualSentences = $this->processResponse($response);
            // Проверяем, что предложение не содержит ошибки
            foreach ($individualSentences as $sentence) {
                if (strpos($sentence, 'Error') === false && strpos($sentence, 'Failed') === false) {
                    $sentences[] = $sentence;
                }
            }
        }

//        foreach ($words as $word) {
//            $response = $this->getSentencesAIStudio($word);
//            $individualSentences = $this->processResponse($response);
//
//            foreach ($individualSentences as $sentence) {
//                if (strpos($sentence, 'Error') === false && strpos($sentence, 'Failed') === false) {
//                    // Переводим предложение на русский язык
//                    $translatedSentence = $this->translateToRussian($sentence);
//
//                    // Добавляем в массив результатов
//                    $sentences[] = [
//                        'original' => $sentence,
//                        'translated' => $translatedSentence,
//                    ];
//                }
//            }
//        }

        return new ApiResponse(compact('sentences'));
    }

    /**
     * Запрос на AI21 Studio и получение ответа
     * @param $word
     * @return string
     * @throws GuzzleException
     */
    private function getSentencesAIStudio($word): string
    {
        $client = new Client();
        // Поместите ваш API ключ в файл .env
        $apiKey = env('STUDIO_AI_API_KEY');
        $prompt = 'Create five simple literary sentences that are not similar to each other in English and separate them with a vertical symbol | using the given word: ' . $word;

        try {
            // Используем правильный URL-адрес для AI21 Studio API
            $response = $client->post('https://api.ai21.com/studio/v1/j2-ultra/complete', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
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
            return 'Error: ' . $e->getMessage();
        }
    }

    /**
     * Обрабатывает ответ API и извлекает предложения
     * @param $response
     * @return array
     */
    private function processResponse(string $response): array
    {
        // Разделяем предложения по разделителю "|"
        $sentencesArray = explode('|', $response);

        // Очищаем каждое предложение от лишних символов и цифр
        $cleanedSentences = [];
        foreach ($sentencesArray as $sentence) {
            // Удаляем цифры и лишние пробелы в начале и конце
            $cleanedSentence = trim(preg_replace('/\d+\.\s*/', '', $sentence));
            if (!empty($cleanedSentence)) {
                $cleanedSentences[] = $cleanedSentence;
            }
        }

        return $cleanedSentences;
    }

//    /**
//     * Переводит текст на русский язык
//     * @param string $text
//     * @return string
//     */
//    private function translateToRussian(string $text): string
//    {
//        // Инициализируем объект для перевода
//        $translator = new GoogleTranslate();
//        // Устанавливаем исходный и целевой языки
//        $translator->setSource('en'); // Исходный язык - английский
//        $translator->setTarget('ru'); // Целевой язык - русский
//
//        // Переводим текст
//        return $translator->translate($text);
//    }
}
