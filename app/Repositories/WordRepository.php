<?php

namespace App\Repositories;

use App\Http\Requests\Word\CreateWordRequest;
use App\Http\Requests\Word\DeleteWordRequest;
use App\Http\Requests\Word\UpdateWordRequest;
use App\Models\Test;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class WordRepository extends CoreRepository
{

    /**
     * Выборка в пагинации страницы слов
     *
     * @param $request
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getWords($request): array
    {
        $vars = $this->getVariablesForTables($request);
        $collection = $this->startConditions()->with('type');

        // search
        $searchArray = $vars['search'] ?? [];
        if (! empty($searchArray) && ! empty($searchArray[0])) {
            foreach ($searchArray as $word) {
                // word language
                $language = $this->getLanguage($word);
                if ($language) {
                    // Select a column name depending on the language
                    $column_name = $language === 'en' ? 'word' : 'translation';
                    if ($column_name == 'translation') {
                        $word = '%'.$word;
                    }
                    $collection = $collection->Orwhere($column_name, 'like', $word.'%');
                } else {
                    // Если не удалось определить язык, ищем в обоих полях
                    $collection = $collection->where(function ($query) use ($word) {
                        $query->where('word', 'like', $word.'%')
                            ->orWhere('translation', 'like', '%'.$word.'%');
                    });
                }
            }
        }

        // выбрать указанные типы слов
        if ($vars['selection_type_id']) {
            $collection = $collection->where('type_id', $vars['selection_type_id']);
        }

        $total_count = $collection->get()->count();

        // sort
        if ($vars['sort_column'] && $vars['sort_type']) {
            if ($vars['sort_column'] == 'letter') {
                $collection = $collection->orderBy('word', $vars['sort_type']);
            }
        }

        // выбрать минимальное время в базе
        $minUpdatedAt = $collection->min('updated_at');
        // Начинает отбор с указанной строки в базе
        if ($minUpdatedAt) {
            $collection = $collection->where('updated_at', '>=', $minUpdatedAt);
        }

        $list = $collection
            ->orderBy('id', 'desc') // сортировка на убывание
            ->skip($vars['offset']) // Пропускаем нужное количество записей
            ->take($vars['limit'])  // Берем нужное количество записей
            ->get();

        $types = $this->getDynamicModelClone('WordType')::get();
        $colors = config('program.type.color');

        return compact('total_count', 'list', 'types', 'colors');
    }

    public function createWord(CreateWordRequest $request)
    {
        DB::beginTransaction();

        try {
            // 1 Создаем слово
            $wordData = $request->except('arr_new_sentences');
            $word = $this->startConditions()::create($wordData);

            if (! empty($request->arr_new_sentences)) {
                // Вставляем новые предложения
                $this->insertSentences($request->arr_new_sentences);
                // Вставляем новые слова
                $this->insertNewWords($request->arr_new_sentences);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        return $word;
    }

    public function updateWord(UpdateWordRequest $request)
    {
        DB::beginTransaction();

        try {
            // Обновляем слово
            $word = $this->updateWordData($request);

            if (! empty($request->arr_new_sentences)) {
                // Вставляем новые предложения
                $this->insertSentences($request->arr_new_sentences);
                // Вставляем новые слова
                $this->insertNewWords($request->arr_new_sentences);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        return $word;
    }

    public function deleteWord(DeleteWordRequest $request)
    {
        $this->startConditions()::where('id', $request->id)->delete();
    }

    /**
     * Обновить статус слова (known / unknown).
     */
    public function updateWordStatus(int $wordId, string $status): void
    {
        try {
            $model = $this->getDynamicModelClone('Word');
        } catch (\Throwable $e) {
            return;
        }

        $word = $model::find($wordId);
        if (!$word) {
            return;
        }

        if ($status === 'unknown') {
            $word->is_known = 0;

            // Если ещё не было порядка — проставляем next order
            if (is_null($word->unknown_order)) {
                $max = $model::max('unknown_order');
                $word->unknown_order = $max ? $max + 1 : 1;
            }
        } elseif ($status === 'known') {
            $word->is_known = 1;
            $word->unknown_order = null; // сбросить порядок
        }

        $word->save();
    }

    /**
     * Получить следующее слово для изучения:
     *
     * Логика:
     * 1. Если это первый запрос (word_id и unknown_order = null) —
     *    берём "не знание" с наименьшим unknown_order.
     * 2. Иначе пробуем найти "не знание" с unknown_order > текущего.
     * 3. Если не нашли — берём слово с id > текущего.
     * 4. К найденному слову подгружаем предложения.
     *
     * @param array $validateData
     * @return object|null
     */
    public function getNextLearnWord(
        array $validateData = ['word_id'=>null,'unknown_order'=>null,'status'=>null]
    ): ?object
    {
        try {
            $model = $this->getDynamicModelClone('Word');
        } catch (\Throwable $e) {
            return null;
        }

        $nextWord = null;
        // достаём из параметров или из сессии
        $wordId       = $validateData['word_id'] ?? null;
        $unknownOrder = $validateData['unknown_order'] ?? null;

        // 1. в первом запросе - свойства приходят - null
        // выбрать слово "не знания" у которого наименьший unknown_order
        if (!$wordId && !$unknownOrder) {
            $nextWord = $model::query()
                ->where('is_known', 0)
                ->whereNotNull('unknown_order')
                ->orderBy('unknown_order', 'asc')
                ->first();
        }

        // 2. нужно выбрать слово "не знания" у которого unknown_order на 1 больше чем у предыдущего
        if (!$nextWord && $wordId && $unknownOrder) {
            $nextWord = $model::query()
                ->where('is_known', 0)
                ->where('unknown_order', '>', $unknownOrder)
                ->orderBy('unknown_order', 'asc')
                ->first();
        }

        // 3. если нет "Следующего" слова выбрать слово у которого id больше предыдущего
        if ($wordId && !$nextWord) {
            $nextWord = $model::query()
                ->where('id', '>', $wordId)
                ->orderBy('id', 'asc')
                ->first();
        }

        // 4 Подгружаем предложения с этим словом
        if ($nextWord) {
            try {
                $sentenceModel = $this->getDynamicModelClone('Sentence');
                $nextWord->sentences = $sentenceModel::where('sentence', 'like', '%'.$nextWord->word.'%')->get();
            } catch (\Throwable $e) {
                $nextWord->sentences = collect();
            }
        }

        return $nextWord;
    }

    protected function updateWordData(UpdateWordRequest $request)
    {
        $word = $this->startConditions()::findOrFail($request->word_id);

        $data = $request->except(['arr_new_sentences',
            'word_id',
        ]);

        $word->timestamps = false;
        $word->fill($data)->save();
        $word->timestamps = true;

        return $word;
    }

    protected function insertSentences(array $sentences)
    {
        $dataToInsert = [];

        foreach ($sentences as $obj) {
            $dataToInsert[] = ['sentence' => $obj['original'],
                'translation' => $obj['translated'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $this->getDynamicModelClone('Sentence')::insert($dataToInsert);
    }

    /**
     * Вставляем новые слова
     *
     * @param $arrSentences
     * @return void
     */
    protected function insertNewWords($arrSentences): void
    {
        foreach ($arrSentences as $obj) {
            // Удаляем лишние пробелы из предложения
            $sentence = preg_replace('|[\s]+|s', ' ', $obj['original']);
            // Разбиваем предложение на массив слов
            $words = explode(' ', trim($sentence));

            // Фильтруем — пропускаем слова, которые состоят только из цифр
            $words = array_filter($words, function($word) {
                return !preg_match('/^\d+$/', $word);
            });

            // Добавляем слова которых нет
            $this->startConditions()::processWords($words);
        }
    }

    protected function getModelClass(): string
    {
        $learnLanguage = ucfirst($this->user->languageUser->learnLanguage->language ?? 'en');

        return "App\\Models\\{$learnLanguage}Word";
    }
}
