<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

/**
 * Main Repositories class that other Repositories classes extend.
 *
 * @package App\Repositories
 */
abstract class Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * Repositories constructor.
     * The model should be an instance of the class for which repository is created.
     *
     * @param Model $model
     */
    public function __construct(Model $model = null)
    {
        $this->model = $model;
    }

    /**
     * @inheritdoc
     */
    public function all($columns = ['*'], $relations = [])
    {
        return $this->model->with($relations)->get($columns);
    }

    /**
     * @inheritdoc
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @inheritDoc
     */
    public function update(Model $model, $data)
    {
        return $model->update($data);
    }

    /**
     * @inheritdoc
     */
    public function updateWhereId(int $id, array $data)
    {
        $this->model->where('id', $id)->update($data);
    }

    /**
     * @inheritdoc
     */
    public function updateWhere(array $whereData, array $data)
    {
        $this->model->newQuery()->where($whereData)->update($data);
    }


    /**
     * @inheritdoc
     */
    public function find($id, $columns = ['*'], $relations = [])
    {
        return $this->model->with($relations)->find($id, $columns);
    }

    /**
     * @inheritdoc
     */
    public function findOrFail($id, $columns = ['*'], $relations = [])
    {
        return $this->model->with($relations)->findOrFail($id, $columns);
    }

    /**
     * @inheritdoc
     */
    public function findOrNew($id, $columns = ['*'], $relations = [])
    {
        return $this->model->with($relations)->findOrNew($id, $columns);
    }

    /**
     * @inheritdoc
     */
    public function firstBy($columnName, $columnValue, $columns = ['*'], $relations = [])
    {
        return $this->model->with($relations)->where($columnName, $columnValue)->first($columns);
    }

    /**
     * @inheritDoc
     */
    public function delete(Model $model)
    {
        return $model->delete();
    }
}
