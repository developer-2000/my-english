<?php
namespace App\Repositories;

use App\Models\Word;
use App\Models\WordType;

class WordRepository extends CoreRepository
{

    public function getWords($request)
    {
        $vars = $this->getVariablesForTables($request);
        $query = $this->startConditions();
        // 1 max count words
        $total_count = $query->get()->count();
        // 2 all types
        $types = WordType::get();
        // 3 all color
        $colors = config('programm.type.color');
        $query = $query->with('type');

        // search
        if($vars['search'] != ''){
            $query = $query->where('word', 'like', $vars['search'] . '%');
        }

        // sort
        if ($vars['sort_column'] && $vars['sort_type']) {
            if ($vars['sort_column'] == 'letter') {
                $query = $query
                    ->orderBy('word', $vars['sort_type']);
            }
        }

        // paginate
        $list = $query
            ->skip($vars['offset'])->take($vars['limit'])
            ->orderBy('id', 'desc')
            ->get();

        return compact('total_count', 'list', 'types', 'colors');
    }


    /**
     * @inheritDoc
     */
    protected function getModelClass()
    {
        return Word::class;
    }
}
