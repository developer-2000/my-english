<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Class CoreRepository
 * @package App\Repositories
 */
abstract class CoreRepository
{

    protected $model;
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->model = app($this->getModelClass());
    }

    protected function startConditions()
    {
        return clone $this->model;
    }

    abstract protected function getModelClass();

    protected function getVariablesForTables(array $data)
    {
        $collected_data = collect($data);
        $selection_type_id = $data['selection_type_id'] !== 'null' ? $data['selection_type_id'] : null;
        $sort_column = $collected_data->get('sortField', null);
        $sort_type = $collected_data->get('sortType', null);
        $sort_type = $sort_type === 'none' ? null : $sort_type;
        // номер страцы выборки
        $page = (int)$collected_data->get('page', 1);
        // кол-во на странице
        $limit = (int)$collected_data->get('perPage', 50);
        // показать все записи
        if($limit < 0){
            $page = 1;
            $limit = 999999;
        }

        $offset = $page * $limit - $limit;
        $search = $collected_data->get('search');
        // Разбиваем строку по пробелам и удаляем пустые элементы
        $search = array_filter(explode(' ', $search));

        return compact('selection_type_id','limit', 'offset', 'search', 'sort_column', 'sort_type');
    }

    protected function getLanguage($string)
    {
        if (preg_match('/[a-zA-Z]/', $string)) {
            return 'en';
        }
        elseif (preg_match('/[а-яА-ЯёЁ]/', $string)) {
            return 'ru';
        }
        return false;
    }

    protected function getDynamicModelClone($string)
    {
        $learnLanguage = ucfirst($this->user->languageUser->learnLanguage->language);
//        return "App\\Models\\{$learnLanguage}{$string}";
        return clone app("App\\Models\\{$learnLanguage}{$string}");
    }

}
