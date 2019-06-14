<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

interface RepositoryInterface
{
    /**
     * Get all records with specified columns.
     *
     * @param array $columns
     * @param array $relations
     * @return \Illuminate\Support\Collection
     */
    public function all($columns = ['*'], $relations = []);

    /**
     * Create new record with given data.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data);

    /**
     * Update given model.
     *
     * @param Model $model
     * @param $data
     * @return bool
     */
    public function update(Model $model, $data);

    /**
     * Update exist model.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateWhereId(int $id, array $data);

    /**
     * Update all records where data is matched with given data.
     *
     * @param array $whereData
     * @param array $data
     * @return mixed
     */
    public function updateWhere(array $whereData, array $data);

    /**
     * Find first record with given columns by the ID.
     *
     * @param $id
     * @param array $columns
     * @param array $relations
     * @return mixed
     */
    public function find($id, $columns = ['*'], $relations = []);

    /**
     * Find first record with given columns by the ID.
     * If the record for given ID does not exist, ModelNotFoundException will be thrown.
     *
     * @param $id
     * @param array $columns
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Collection|Model
     */
    public function findOrFail($id, $columns = ['*'], $relations = []);

    /**
     * Find first record with given columns by the ID.
     * If the record for given ID does not exist, new one will be created.
     *
     * @param $id
     * @param array $columns
     * @param array $relations
     * @return Model
     */
    public function findOrNew($id, $columns = ['*'], $relations = []);

    /**
     * Find first record by given column name.
     *
     * @param $columnName : name for column you are going to search
     * @param $columnValue : searched value
     * @param array $columns : column to take
     * @param array $relations : specified relation for eager loading
     * @return Model
     */
    public function firstBy($columnName, $columnValue, $columns = ['*'], $relations = ['*']);

    /**
     * Delete given model from database.
     *
     * @param Model $model
     * @return bool
     */
    public function delete(Model $model);
}
