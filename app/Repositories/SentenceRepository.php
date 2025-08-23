<?php

namespace App\Repositories;

use App\Http\Requests\Sentence\BindCheckboxSoundRequest;
use App\Http\Requests\Word\SearchWordRequest;
use App\Http\Requests\Word\UpdateSentenceRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SentenceRepository extends CoreRepository
{
    public function getSentences($request): array
    {
        $vars = $this->getVariablesForTables($request);
        $collection = $this->startConditions()->with('sound');

        // 1 Найти все предложения в которых есть все указанные слова
        $searchArray = $vars['search'] ?? [];
        if (! empty($searchArray) && ! empty($searchArray[0])) {
            // word language
            if ($language = $this->getLanguage($searchArray[0])) {
                // Select a column name depending on the language
                $column_name = $language === 'en' ? 'sentence' : 'translation';
                foreach ($searchArray as $word) {
                    // Используем LIKE для более гибкого поиска, нечувствительного к регистру
                    $collection = $collection->whereRaw("LOWER({$column_name}) LIKE ?", ['%'.strtolower($word).'%']);
                }
            }
        }

        // 2 количество выбранных обьектов
        $total_count = $collection->get()->count();

        // 3 Сортируем если указана сортировка
        if ($vars['sort_column'] && $vars['sort_type']) {
            // по предложению
            if ($vars['sort_column'] == 'sentence') {
                $collection = $collection
                    ->orderBy('sentence', $vars['sort_type']);
            } // по чекбоксам озвучки предложений
            elseif ($vars['sort_column'] == 'sound') {
                $collection = $this->startConditions()
                    ->leftJoin('en_sentence_sounds', 'en_sentences.id', '=', 'en_sentence_sounds.sentence_id')
                    ->select('en_sentences.*', DB::raw('en_sentence_sounds.id as sound'))
                    ->orderBy(DB::raw($vars['sort_column']), $vars['sort_type']);
            }
        }

        // 4 Выбрать в пагинации
        $list = $collection
            ->orderBy('priority', 'desc') // Сначала сортируем по priority
            ->orderBy('id', 'desc') // Затем сортируем по id, если priority одинаковый
            ->skip($vars['offset']) // Пропускаем нужное количество записей
            ->take($vars['limit']) // Берем нужное количество записей
            ->get();

        return compact('total_count', 'list');
    }

    public function checkSentenceExists($request): bool
    {
        // Удаляем лишние пробелы из предложения для сравнения
        $sentence = preg_replace('|[\s]+|s', ' ', $request['sentence']);

        // Проверяем существование предложения (без учета регистра)
        return $this->startConditions()
            ->whereRaw('LOWER(sentence) = ?', [strtolower($sentence)])
            ->exists();
    }

    public function checkSentenceExistsForUpdate($request): bool
    {
        // Удаляем лишние пробелы из предложения для сравнения
        $sentence = preg_replace('|[\s]+|s', ' ', $request['sentence']);

        // Проверяем существование предложения (без учета регистра), исключая текущее предложение
        return $this->startConditions()
            ->whereRaw('LOWER(sentence) = ?', [strtolower($sentence)])
            ->where('id', '!=', $request['sentence_id'])
            ->exists();
    }

    public function storeSentences($request): void
    {
        // Удаляем лишние пробелы из предложения
        $sentence = preg_replace('|[\s]+|s', ' ', $request['sentence']);
        // Разбиваем предложение на массив слов
        $words = explode(' ', trim($sentence));

        // добавить слова которых нет
        $this->getDynamicModelClone('Word')::processWords($words);
        // добавить предложение
        $this->startConditions()::create($request);
    }

    public function searchWord(SearchWordRequest $request)
    {
        return $this->getDynamicModelClone('Word')::where('word', 'like', $request->word.'%')->get()->pluck('word');
    }

    public function updateSentence(UpdateSentenceRequest $request)
    {
        return $this->getDynamicModelClone('Sentence')::where('id', $request->sentence_id)->update(Arr::except($request->validated(), 'sentence_id'));
    }

    public function searchSentences(SearchWordRequest $request)
    {
        //        return $this->getDynamicModelClone("Sentence")::where('sentence', 'like', '%' . $request->word . '%')->get();
        return $this->getDynamicModelClone('Sentence')::where('sentence', 'REGEXP', '\\b'.preg_quote($request->word, '\\').'\\b')->get();
    }

    public function bindCheckboxSound(BindCheckboxSoundRequest $request)
    {
        if ($request['status']) {
            $this->getDynamicModelClone('SentenceSound')::firstOrCreate(['sentence_id' => $request->sentence_id]);
        } else {
            $this->getDynamicModelClone('SentenceSound')::where('sentence_id', $request->sentence_id)->delete();
        }
    }

    protected function getModelClass(): string
    {
        $learnLanguage = ucfirst($this->user->languageUser->learnLanguage->language);

        return "App\\Models\\{$learnLanguage}Sentence";
    }
}
