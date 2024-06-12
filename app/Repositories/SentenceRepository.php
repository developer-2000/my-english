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

        // search Найти все элементы в строке базы
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

        $total_count = $collection->get()->count();

        // sort
        if ($vars['sort_column'] && $vars['sort_type']) {
            if ($vars['sort_column'] == 'sentence') {
                $collection = $collection
                    ->orderBy('sentence', $vars['sort_type']);
            }
            // сортировка checkbox по имеющейся связи
            elseif ($vars['sort_column'] == 'sound') {
                $collection = $this->startConditions()
                    ->leftJoin('sentence_sounds', 'sentences.id', '=', 'sentence_sounds.sentence_id')
                    ->select('sentences.*', DB::raw('sentence_sounds.id as sound'))
                    ->orderBy(DB::raw($vars['sort_column']), $vars['sort_type']);
            }
        }

        // paginate
        $list = $collection
            ->skip($vars['offset'])->take($vars['limit'])
            ->orderBy('id', 'desc')
            ->get();

        return compact('total_count', 'list');
    }

    public function storeSentences($request): void
    {
        $request['sentence'] = preg_replace('|[\s]+|s', ' ', $request['sentence']);
        $words = explode(" ", trim($request['sentence']));

        // добавить слова которых нет
        foreach ($words as $key => $word){
            // Удаляем пробелы и точки из слова
            $cleanedWord = str_replace([' ', '.'], '', $word);

            Word::firstOrCreate(
                ['word' => $cleanedWord],
                ['translation' => '', 'description' => '""']
            );
        }
        // добавить предложение
        Sentence::create($request);
    }

    protected function getModelClass(): string
    {
        return Sentence::class;
    }
}
