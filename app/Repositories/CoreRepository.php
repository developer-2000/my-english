<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CoreRepository
 * @package App\Repositories
 */
abstract class CoreRepository
{

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Model|mixed
     */
    protected function startConditions()
    {
        return clone $this->model;
    }

    /**
     * @var \Illuminate\Contracts\Foundation\Application|Model|mixed
     */
    protected $model;

    /**
     * @return mixed
     */
    abstract protected function getModelClass();

    /**
     * CoreRepository constructor.
     */
    public function __construct()
    {
        $this->model = app($this->getModelClass());
    }

    /**
     * data from admin request for table (Vue Good Table)
     *
     * @param array $data
     * @return array
     */
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
        // с какой строки выбирать
        $offset = $page * $limit - $limit;
        $search = $collected_data->get('search');

        return compact('limit', 'offset', 'search', 'sort_column', 'sort_type');
    }

}
