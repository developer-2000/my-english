<?php
namespace App\Repositories;

use App\Models\Sentence;
use App\Models\Test;
use App\Models\Word;
use Illuminate\Support\Facades\DB;

class SentenceRepository extends CoreRepository
{

    public function getSentences($request)
    {
        $vars = $this->getVariablesForTables($request);
        $collection = $this->startConditions()->with('sound');

        // search Найти все элементы в строке базы
        if(!empty($vars['search'])){
            $searchArray = $vars['search'];
            foreach ($searchArray as $word) {
                $collection = $collection->where('sentence', 'like', '% ' . $word . '%');
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

    public function storeSentences($request)
    {
        $request['sentence'] = preg_replace('|[\s]+|s', ' ', $request['sentence']);
        $words = explode(" ", trim($request['sentence']));

        // добавить слова которых нет
        foreach ($words as $key => $word){
            // 1 вставляю множественное если есть противоположное в базе
            if( !$this->updateToManyWord($word) && !$this->updateToOneWord($word) ){
                if(is_null($this->checkMany($word))){
                    $coll = \App\Models\Word::create(
                        ['word' => $word],
                        ['translation' => '']
                    );
                }
            }
        }
        // добавить предложение
        $sentence = Sentence::create($request);

        return 1;
    }

    // добавить к единственному 's
    private function updateToManyWord($word){
        $string = '';
        $coll = null;

        // найти в базе единственный род слова
        if(strlen($word) >= 3){
            if(substr($word, -2) == 'es'){
                $string = substr($word, 0, -2);
                $coll = \App\Models\Word::where( 'word', $string ) ->first();
            }
            if(is_null($coll) && substr($word, -1) == 's'){
                $string = substr($word, 0, -1);
                $coll = \App\Models\Word::where( 'word', $string ) ->first();
            }
        }

        if(!is_null($coll)){
            $coll = \App\Models\Word::where( 'id', $coll->id ) ->update([ 'word'=>($coll->word.'\'s') ]);
            return true;
        }

        return false;
    }
    // добавить к множественному 's
    private function updateToOneWord($word){
        $coll = \App\Models\Word::where( 'word', $word.'s' ) ->first();
        if(!is_null($coll)){
            $coll = \App\Models\Word::where('id', $coll->id) ->update([
                'word'=>($word.'\'s')
            ]);
            return true;
        }

        return false;
    }
    // проверка существования множественного
    private function checkMany($word){
        $string1 = '';
        $string2 = '';

        // mistakes ищу в базе mistake's
        if(substr($word, -2) == 'es'){
            $string1 = substr($word, 0, -2).'\'s';
        }
        if(substr($word, -1) == 's'){
            $string2 = substr($word, 0, -1).'\'s';
        }

        return Word::where(['word' => $string1])
            ->orWhere(['word' => $string2])
            ->orWhere(['word' => $word.'\'s'])
            ->orWhere(['word' => $word])
            ->first();
    }



    /**
     * @inheritDoc
     */
    protected function getModelClass()
    {
        return Sentence::class;
    }
}
