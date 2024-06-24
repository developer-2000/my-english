<?php

namespace App\Repositories;

use App\Models\EnSentence;
use App\Models\Word;
use App\Models\EnWord;
use Carbon\Carbon;

class LearnWordRepository extends CoreRepository
{
    /**
     * Получить слово для изучения на основе переданного запроса.
     *
     * @param $request
     * @return Word|null
     */
    public function getLearnWord($request)
    {
        // Проверка наличия параметра last_updated_at
        if (!is_null($request->last_updated_at)) {
            $lastUpdatedAt = Carbon::parse($request->last_updated_at);

            // Поиск слова с updated_at, более старым, чем last_updated_at
            $latestWord = $this->getDynamicModelClone("Word")::where('updated_at', '<', $lastUpdatedAt)
                ->orderBy('updated_at', 'desc')
                ->first();
        } else {
            // Если last_updated_at не указан, выбираем самое свежее слово
            $latestWord = $this->getDynamicModelClone("Word")::orderBy('updated_at', 'desc')->first();
        }

        // Если слово найдено, выбираем предложения с его участием
        if ($latestWord) {
            $latestWord->sentences = $this->getDynamicModelClone("Sentence")::where('sentence', 'like', '%' . $latestWord->word . '%')->get();
            return $latestWord;
        }

        // Если слово не найдено, возвращаем null
        return null;
    }

    /**
     * Обновить метку времени 'updated_at' у слова на основе действия.
     *
     * @param int $wordId
     * @param string $action
     * @return void
     */
    public function updateWordTimestamp($wordId, $action)
    {
        $newDate = null;

        // Если действие 'up', обновить время до самой свежей
        if ($action === 'up') {
            $newDate = now();
        }
        // Если действие 'down', обновить время до самой старой
        elseif ($action === 'down') {
            $oldestWord = $this->getDynamicModelClone("Word")::orderBy('updated_at', 'asc')->first();
            if ($oldestWord) {
                // Отнимаем 1 секунду от самой старой даты
                $newDate = Carbon::parse($oldestWord->updated_at)->subSeconds(1);
            }
        }

        // Если новая дата установлена, обновляем слово
        if (!is_null($newDate)) {
            $wordToUpdate = $this->getDynamicModelClone("Word")::where('id', $wordId)->first();
            if ($wordToUpdate) {
                $wordToUpdate->update(['updated_at' => $newDate]);
            }
        }
    }

    protected function getModelClass(): string
    {
        $learnLanguage = ucfirst($this->user->languageUser->learnLanguage->language);
        return "App\\Models\\{$learnLanguage}Word";
    }
}
