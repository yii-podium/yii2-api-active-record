<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Repositories;

use LogicException;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Throwable;
use yii\base\NotSupportedException;
use yii\data\ActiveDataProvider;
use yii\data\DataFilter;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

use function is_int;

trait ActiveRecordRepositoryTrait
{
    private array $errors = [];

    private ?ActiveDataProvider $collection = null;

    abstract public function getActiveRecordClass(): string;

    abstract public function getModel(): ActiveRecord;

    abstract public function setModel(?ActiveRecord $model): void;

    public function getCollection(): ActiveDataProvider
    {
        if (null === $this->collection) {
            throw new LogicException('You need to call fetchAll() or setCollection() first!');
        }

        return $this->collection;
    }

    public function setCollection(ActiveDataProvider $collection): void
    {
        $this->collection = $collection;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param int|string|array $id
     */
    public function fetchOne($id): bool
    {
        $modelClass = $this->getActiveRecordClass();

        /** @var ActiveRecord $modelClass */
        $model = $modelClass::find()->where(['id' => $id])->one();

        if (null === $model) {
            return false;
        }

        $this->setModel($model);

        return true;
    }

    /**
     * @param mixed|null $filter
     * @param mixed|null $sort
     * @param mixed|null $pagination
     *
     * @throws NotSupportedException
     */
    public function fetchAll($filter = null, $sort = null, $pagination = null): void
    {
        $modelClass = $this->getActiveRecordClass();

        /** @var ActiveRecord $modelClass */
        $query = $modelClass::find();

        if (null !== $filter) {
            if (!$filter instanceof DataFilter) {
                throw new NotSupportedException('Only filters implementing yii\data\DataFilter are supported!');
            }

            $filterConditions = $filter->build();
            if (false !== $filterConditions && [] !== $filterConditions) {
                $query->andWhere($filterConditions);
            }
        }

        $dataProvider = new ActiveDataProvider(['query' => $query]);

        if (null !== $sort) {
            $dataProvider->setSort($sort);
        }

        if (null !== $pagination) {
            $dataProvider->setPagination($pagination);
        }

        $this->setCollection($dataProvider);
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function delete(): bool
    {
        return is_int($this->getModel()->delete());
    }

    public function edit(array $data = []): bool
    {
        $model = $this->getModel();

        if ([] !== $data && !$model->load($data, '')) {
            return false;
        }

        if (!$model->validate()) {
            $this->errors = $model->errors;

            return false;
        }

        return $model->save(false);
    }

    public function getGroups(): array
    {
        $groupsRepositories = [];

        $groups = $this->getModel()->groups;
        foreach ($groups as $group) {
            $repository = new GroupRepository();
            $repository->setModel($group);
            $groupsRepositories[] = $repository;
        }

        return $groupsRepositories;
    }

    public function hasGroups(array $groups): bool
    {
        $existingGroups = $this->getModel()->groups;
        if (count($existingGroups) < count($groups)) {
            return false;
        }

        /** @var GroupRepositoryInterface $group */
        foreach ($groups as $group) {
            $groupId = $group->getId();
            $groupFound = false;
            foreach ($existingGroups as $existingGroup) {
                if ($existingGroup->id === $groupId) {
                    $groupFound = true;
                    break;
                }
            }
            if (!$groupFound) {
                return false;
            }
        }

        return true;
    }

    public function join(GroupRepositoryInterface $group): bool
    {
        try {
            $this->getModel()->link('groups', $group->getModel());
        } catch (Throwable $exception) {
            return false;
        }

        return true;
    }

    public function leave(GroupRepositoryInterface $group): bool
    {
        try {
            $this->getModel()->unlink('groups', $group->getModel(), true);
        } catch (Throwable $exception) {
            return false;
        }

        return true;
    }
}
