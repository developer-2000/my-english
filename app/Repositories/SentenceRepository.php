<?php
namespace App\Repositories;

use App\Models\Sentence;
use App\Models\Word;
use Illuminate\Support\Facades\DB;

class SentenceRepository extends CoreRepository
{

    public function getSentences($request): array
    {
        $vars = $this->getVariablesForTables($request);
        $collection = $this->startConditions()->with('sound');

        // 1 Найти все предложения в которых есть все указанные слова
        $searchArray = $vars['search'] ?? [];
        if (!empty($searchArray) && !empty($searchArray[0])) {
            // word language
            if($language = $this->getLanguage($searchArray[0])){
                // Select a column name depending on the language
                $column_name = $language === 'en' ? 'sentence' : 'translation';
                foreach ($searchArray as $word) {
                    $collection = $collection->where($column_name, 'like', '%' . $word . '%');
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
            }
            // по чекбоксам озвучки предложений
            elseif ($vars['sort_column'] == 'sound') {
                $collection = $this->startConditions()
                    ->leftJoin('sentence_sounds', 'sentences.id', '=', 'sentence_sounds.sentence_id')
                    ->select('sentences.*', DB::raw('sentence_sounds.id as sound'))
                    ->orderBy(DB::raw($vars['sort_column']), $vars['sort_type']);
            }
        }

        // 4 Выбрать в пагинации
        $list = $collection
            ->skip($vars['offset'])->take($vars['limit'])
            ->orderBy('id', 'desc')
            ->get();

        return compact('total_count', 'list');
    }

    public function storeSentences($request): void
    {
        // Удаляем лишние пробелы из предложения
        $sentence = preg_replace('|[\s]+|s', ' ', $request['sentence']);
        // Разбиваем предложение на массив слов
        $words = explode(" ", trim($sentence));

        // добавить слова которых нет
        Word::processWords($words);
        // добавить предложение
        Sentence::create($request);
    }

    protected function getModelClass(): string
    {
        return Sentence::class;
    }
}
