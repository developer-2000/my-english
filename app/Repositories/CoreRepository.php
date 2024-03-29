<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CoreRepository
 * @package App\Repositories
 */
abstract class CoreRepository
{

    protected $model;

    public function __construct()
    {
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

        return compact('limit', 'offset', 'search', 'sort_column', 'sort_type');
    }

}
