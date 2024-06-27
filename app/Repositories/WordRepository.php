<?php
namespace App\Repositories;

use App\Http\Requests\Word\CreateWordRequest;
use App\Http\Requests\Word\DeleteWordRequest;
use App\Http\Requests\Word\UpdateWordRequest;
use Illuminate\Support\Facades\DB;

class WordRepository extends CoreRepository
{

    public function getWords($request)
    {
        $vars = $this->getVariablesForTables($request);
        $collection = $this->startConditions()->with('type');

        // search
        $searchArray = $vars['search'] ?? [];
        if (!empty($searchArray) && !empty($searchArray[0])) {
                // word language
                if($language = $this->getLanguage($searchArray[0])){
                    // Select a column name depending on the language
                    $column_name = $language === 'en' ? 'word' : 'translation';
                    foreach ($searchArray as $word) {
                        if($column_name == 'translation'){
                            $word = '%' . $word;
                        }
                        $collection = $collection->Orwhere($column_name, 'like', $word . '%');
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
                $collection = $collection
                    ->orderBy('word', $vars['sort_type']);
            }
        }

        // выбрать минимальное время в базе
        $minUpdatedAt = $collection->min('updated_at');
        // Начинает отбор с указанной строки в базе
        if ($minUpdatedAt) {
            $collection = $collection->where('updated_at', '>=', $minUpdatedAt);
        }

        // paginate
        $list = $collection
            ->skip($vars['offset'])->take($vars['limit'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $types = $this->getDynamicModelClone("WordType")::get();
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

            if (!empty($request->arr_new_sentences)) {
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

            if (!empty($request->arr_new_sentences)) {
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
        $this->startConditions()::where('id',$request->id)
            ->delete();
    }

    protected function updateWordData(UpdateWordRequest $request)
    {
        $word = $this->startConditions()::findOrFail($request->word_id);

        $data = $request->except(['arr_new_sentences','word_id']);

        $word->timestamps = false;
        $word->fill($data)->save();
        $word->timestamps = true;

        return $word;
    }

    protected function insertSentences(array $sentences)
    {
        $dataToInsert = [];

        foreach ($sentences as $obj) {
            $dataToInsert[] = [
                'sentence' => $obj['original'],
                'translation' => $obj['translated'],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        $this->getDynamicModelClone("Sentence")::insert($dataToInsert);
    }

    /**
     * Вставляем новые предложения
     * @param $request
     * @return void
     */
    protected function insertNewWords($arrSentences): void
    {
        foreach ($arrSentences as $obj) {
            // Удаляем лишние пробелы из предложения
            $sentence = preg_replace('|[\s]+|s', ' ', $obj['original']);
            // Разбиваем предложение на массив слов
            $words = explode(" ", trim($sentence));
            // добавить слова которых нет
            $this->startConditions()::processWords($words);
        }
    }

    protected function getModelClass(): string
    {
        $learnLanguage = ucfirst($this->user->languageUser->learnLanguage->language);
        return "App\\Models\\{$learnLanguage}Word";
    }
}
