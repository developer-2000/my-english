<?php

namespace App\Repositories;

use App\Http\Requests\Word\CreateWordRequest;
use App\Http\Requests\Word\DeleteWordRequest;
use App\Http\Requests\Word\UpdateWordRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WordRepository extends CoreRepository
{
    public function getWords($request)
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

        // paginate
        //        $list = $collection->skip($vars['offset'])
        //            ->take($vars['limit'])
        //            ->orderBy('updated_at', 'desc')
        //            ->get();
        $list = $collection
            ->orderBy('updated_at', 'desc') // Сначала сортируем по priority
            ->orderBy('id', 'desc') // Затем сортируем по id, если priority одинаковый
            ->skip($vars['offset']) // Пропускаем нужное количество записей
            ->take($vars['limit']) // Берем нужное количество записей
            ->get();

        $types = $this->getDynamicModelClone('WordType')::get();
        $colors = config('program.type.color');

        return compact('total_count', 'list', 'types', 'colors');
    }

    public function createWord(CreateWordRequest $request)
    {
        DB::beginTransaction();

        try {
            // Создаем слово с помощью метода create
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
     * Получить слово для изучения на основе переданного запроса.
     *
     * @return null
     */
    public function getLearnWord($request)
    {
        // Проверка наличия параметра last_updated_at
        if (! is_null($request->last_updated_at)) {
            $lastUpdatedAt = Carbon::parse($request->last_updated_at);

            // Поиск слова с updated_at, более старым, чем last_updated_at
            $latestWord = $this->getDynamicModelClone('Word')::where('updated_at', '<', $lastUpdatedAt)
                ->orderBy('id', 'desc') // Берем запись с наибольшим ID при равном priority
                ->first();
        } else {
            // Если last_updated_at не указан, выбираем самое свежее слово
            $latestWord = $this->getDynamicModelClone('Word')::orderBy('updated_at', 'desc')
                ->orderBy('id', 'desc') // Берем запись с наибольшим ID при равном priority
                ->first();
        }

        // Если слово найдено, выбираем предложения с его участием
        if ($latestWord) {
            $latestWord->sentences = $this->getDynamicModelClone('Sentence')::where('sentence', 'like', '%'.$latestWord->word.'%')->get();

            return $latestWord;
        }

        // Если слово не найдено, возвращаем null
        return null;
    }

    /**
     * Обновить метку времени 'updated_at' у слова на основе действия.
     *
     * @param  int  $wordId
     * @param  string  $action
     * @return void
     */
    public function updateWordTimestamp($wordId, $action)
    {
        $newDate = null;

        // Если действие 'up', обновить время до самой свежей
        if ($action === 'up') {
            $newDate = now();
        } // Если действие 'down', обновить время до самой старой
        elseif ($action === 'down') {
            $oldestWord = $this->getDynamicModelClone('Word')::orderBy('updated_at', 'asc')->first();
            if ($oldestWord) {
                // Отнимаем 1 секунду от самой старой даты
                $newDate = Carbon::parse($oldestWord->updated_at)->subSeconds(1);
            }
        }

        // Если новая дата установлена, обновляем слово
        if (! is_null($newDate)) {
            $wordToUpdate = $this->getDynamicModelClone('Word')::where('id', $wordId)->first();
            if ($wordToUpdate) {
                $wordToUpdate->update(['updated_at' => $newDate]);
            }
        }
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
     * Вставляем новые предложения
     *
     * @param  $request
     */
    protected function insertNewWords($arrSentences): void
    {
        foreach ($arrSentences as $obj) {
            // Удаляем лишние пробелы из предложения
            $sentence = preg_replace('|[\s]+|s', ' ', $obj['original']);
            // Разбиваем предложение на массив слов
            $words = explode(' ', trim($sentence));
            // добавить слова которых нет
            $this->startConditions()::processWords($words);
        }
    }

    protected function getModelClass(): string
    {
        $learnLanguage = ucfirst($this->user->languageUser->learnLanguage->language ?? 'en');

        return "App\\Models\\{$learnLanguage}Word";
    }
}
