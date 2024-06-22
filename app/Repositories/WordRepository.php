<?php
namespace App\Repositories;

use App\Http\Requests\Word\CreateWordRequest;
use App\Http\Requests\Word\UpdateWordRequest;
use App\Models\EnSentence;
use App\Models\EnWord;
use Illuminate\Support\Facades\DB;
use App\Models\EnWordType;

class WordRepository extends CoreRepository
{
    public function getWords($request)
    {
        $vars = $this->getVariablesForTables($request);
        $collection = $this->startConditions()->with('type');

//        return $collection->get();

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

        $types = EnWordType::get();
        $colors = config('program.type.color');

        return compact('total_count', 'list', 'types', 'colors');
    }

    public function createWord(CreateWordRequest $request)
    {
        DB::beginTransaction();

        try {
            // Создаем слово с помощью метода create
            $wordData = $request->except('arr_new_sentences');
            $word = EnWord::create($wordData);

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

    protected function updateWordData(UpdateWordRequest $request)
    {
        $word = EnWord::findOrFail($request->id);

        $data = $request->except('arr_new_sentences');

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

        EnSentence::insert($dataToInsert);
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
            EnWord::processWords($words);
        }
    }

    protected function getModelClass(): string
    {
        return EnWord::class;
    }
}
