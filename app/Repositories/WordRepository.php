<?php
namespace App\Repositories;

use App\Models\Test;
use App\Models\Word;
use App\Models\WordType;

class WordRepository extends CoreRepository
{

    public function getWords($request)
    {
        $vars = $this->getVariablesForTables($request);
        $collection = $this->startConditions()->with('type');

        // search
        if(!empty($vars['search'])){
            $searchArray = $vars['search'];

            if (!empty($searchArray[0])) {
                // word language
                if($language = $this->getLanguage($searchArray[0])){
                    // Select a column name depending on the language
                    $column_name = $language === 'en' ? 'word' : 'translation';
                    foreach ($searchArray as $word) {
                        $collection = $collection->Orwhere($column_name, 'like', $word . '%');
                    }
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

        // paginate
        $list = $collection
            ->skip($vars['offset'])->take($vars['limit'])
            ->orderBy('id', 'desc')
            ->get();

        $types = WordType::get();
        $colors = config('programm.type.color');

        return compact('total_count', 'list', 'types', 'colors');
    }


    /**
     * @inheritDoc
     */
    protected function getModelClass()
    {
        return Word::class;
    }

    private function getLanguage($string)
    {
        if (preg_match('/[a-zA-Z]/', $string)) {
            return 'en';
        }
        elseif (preg_match('/[а-яА-ЯёЁ]/', $string)) {
            return 'ru';
        }
        return false;
    }
}
