<?php

declare (strict_types = 1);

namespace App\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    const DEFAULT_LIMIT = 10;

    /**
     * @param Model $model
     */
    public function setModel(Model $model): void;

    /**
     * @return string
     */
    public function getModelClassName(): string;

    /**
     * @param string $orderBy
     * @param string $direction
     * @return Model|null
     */
    public function getFirst(string $orderBy = '', string $direction = ''):  ? Model;

    /**
     * @param string $name
     * @return Model|null
     */
    public function getFirstByName(string $name): ?Model;

    /**
     * @param array $with
     * @return Collection|null
     */
    public function getAll(array $with = []) :  ? Collection;

    /**
     * @param string $orderKey
     * @param string $direction
     * @param int $limit
     * @return LengthAwarePaginator
     */
    public function getPaginated(string $orderKey = 'created_at', string $direction = 'desc', int $limit = self::DEFAULT_LIMIT) : LengthAwarePaginator;

    /**
     * @param int $id
     * @param array $with
     * @return Model|null
     */
    public function getById(int $id, array $with = []):  ? Model;

    /**
     * @param array $ids
     * @param array $with
     * @return Collection|null
     */
    public function getByIds(array $ids, array $with = []) :  ? Collection;

    /**
     * @return Model
     */
    public function getNewestByCreatedAt() :  ? Model;

    /**
     * @return Model
     */
    public function getNewestByUpdatedAt() :  ? Model;

    /**
     * @param array $input
     */
    public function create(array $input) : void;

    /**
     * @param Model $model
     * @param array $input
     */
    public function update(Model $model, array $input): void;

    /**
     * @param array $input
     * @return Model
     */
    public function createAndReturn(array $input): Model;

    /**
     * @param Model $model
     * @param array $input
     * @return Model
     */
    public function updateAndReturn(Model $model, array $input): Model;

    /**
     * @param Model $model
     */
    public function delete(Model $model): void;
}
