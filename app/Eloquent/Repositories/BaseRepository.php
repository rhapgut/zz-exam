<?php

declare (strict_types = 1);

namespace App\Eloquent\Repositories;

use App\Contracts\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface
{
    /** @var object */
    protected $model;

    /** @var */
    private $modelClassName;

    /**
     * @inheritdoc
     */
    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    /**
     * @inheritdoc
     */
    public function getModelClassName(): string
    {
        return $this->modelClassName = get_class($this->model);
    }

    /**
     * @inheritdoc
     */
    public function getFirst(string $orderBy = '', string $direction = 'asc'):  ? Model
    {
        $modelName = $this->getModelClassName();
        if ($orderBy) {
            if (in_array($direction, ['asc', 'desc'])) {
                return $modelName::orderBy($orderBy, $direction)->first();
            } else {
                return $modelName::orderBy($orderBy, 'asc')->first();
            }
        } else {
            return $modelName::orderBy('id', 'asc')->first();
        }
    }

    /**
     * @inheritdoc
     */
    public function getFirstByName(string $name): ?Model
    {
        $modelName = $this->getModelClassName();
        return $modelName::where('name', $name)->first();
    }

    /**
     * @inheritdoc
     */
    public function getAll(array $with = []) :  ? Collection
    {
        $modelName = $this->getModelClassName();
        return $modelName::with($with)->get();
    }

    /**
     * @inheritdoc
     */
    public function getPaginated(string $orderKey = 'created_at', string $direction = 'desc', int $limit = self::DEFAULT_LIMIT) : LengthAwarePaginator
    {
        $modelName = $this->getModelClassName();
        return $modelName::orderBy($orderKey, $direction)->paginate($limit);
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id, array $with = []):  ? Model
    {
        $modelName = $this->getModelClassName();
        return $modelName::with($with)->find($id);
    }

    /**
     * @inheritdoc
     */
    public function getByIds(array $ids, array $with = []) :  ? Collection
    {
        $modelName = $this->getModelClassName();
        return $modelName::whereIn($this->model->getPrimaryKey(), $ids)->with($with)->get();
    }

    /**
     * @inheritdoc
     */
    public function getNewestByCreatedAt() :  ? Model
    {
        $modelName = $this->getModelClassName();
        return $modelName::orderBy('created_at', 'desc')->first();
    }

    /**
     * @inheritdoc
     */
    public function getNewestByUpdatedAt() :  ? Model
    {
        $modelName = $this->getModelClassName();
        return $modelName::orderBy('updated_at', 'desc')->first();
    }

    /**
     * @inheritdoc
     */
    public function create(array $input) : void
    {
        $modelName = $this->getModelClassName();
        $model = new $modelName();
        $this->update($model, $input);
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $input): void
    {
        foreach ($model->getFillable() as $column) {
            if (array_key_exists($column, $input)) {
                $model->$column = array_get($input, $column);
            }
        }
        $model->save();
    }

    /**
     * @inheritdoc
     */
    public function createAndReturn(array $input): Model
    {
        $modelName = $this->getModelClassName();
        $model = new $modelName();
        return $this->updateAndReturn($model, $input);
    }

    /**
     * @inheritdoc
     */
    public function updateAndReturn(Model $model, array $input): Model
    {
        foreach ($model->getFillable() as $column) {
            if (array_key_exists($column, $input)) {
                $model->$column = array_get($input, $column);
            }
        }
        $model->save();
        return $model;
    }

    /**
     * @inheritdoc
     */
    public function delete(Model $model): void
    {
        $model->delete();
    }
}
