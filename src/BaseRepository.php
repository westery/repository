<?php

namespace Westery\Repository;

use Illuminate\Database\Eloquent\Model;

/**
 * Base Repository for all Repositories
 * 基类Repository
 * Class BaseRepository
 * @package App\Repositories
 */
class BaseRepository
{
    /**
     * @var Model $model
     */
    public $model;

    /**
     * get pagination object
     * 获取分页对象
     * @param null $limit
     * @param array $column
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($limit = null, $column = ['*'])
    {
        return $this->model->newQuery()->paginate($limit,$column);
    }

    /**
     * find object by primary key
     * 根据主键查询对象
     * @param $id
     * @param array $columns
     * @return mixed|static
     */
    public function find($id,$columns=['*'])
    {
        return $this->model->find($id,$columns);
    }

    /**
     * find object by field
     * 根据字段查询对象
     * @param $field
     * @param $value
     * @param  $columns
     * @return Model|null|static
     */
    public function findByField($field, $value,$columns=['*'])
    {
        return $this->model->where($field, $value)->first($columns);
    }

    /**
     * create object and save to databases
     * 创建并存入实例对象
     * @param array $data
     * @return $this|Model
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * update the object by id
     * 更新模型对象
     * @param array $data
     * @param $id
     * @return bool
     */
    public function update(array $data,$id)
    {
        return $this->model->find($id)->update($data);
    }

    /**
     * delete the object by id
     * 删除对象
     * @param $id
     * @return bool|null
     */
    public function delete($id)
    {
        return $this->model->find($id)->delete();
    }



}