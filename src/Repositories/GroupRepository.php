<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Repositories;

use LogicException;
use Podium\ActiveRecordApi\ActiveRecords\GroupActiveRecord;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;

final class GroupRepository implements GroupRepositoryInterface
{
    use ActiveRecordRepositoryTrait;

    public string $activeRecordClass = GroupActiveRecord::class;

    private ?GroupActiveRecord $model = null;

    public function getActiveRecordClass(): string
    {
        return $this->activeRecordClass;
    }

    public function getModel(): GroupActiveRecord
    {
        if (null === $this->model) {
            throw new LogicException('You need to call fetchOne() or setModel() first!');
        }

        return $this->model;
    }

    public function setModel(?ActiveRecord $model): void
    {
        if (!$model instanceof GroupActiveRecord) {
            throw new LogicException('You need to pass Podium\ActiveRecordApi\ActiveRecords\GroupActiveRecord!');
        }

        $this->model = $model;
    }

    public function getId(): int
    {
        return $this->getModel()->id;
    }

    /**
     * @throws NotSupportedException
     */
    public function getParent(): RepositoryInterface
    {
        throw new NotSupportedException('Group does not have parent!');
    }

    public function create(array $data = []): bool
    {
        /** @var GroupActiveRecord $group */
        $group = new $this->activeRecordClass();

        if (!$group->load($data, '')) {
            return false;
        }

        if (!$group->save()) {
            $this->errors = $group->errors;

            return false;
        }

        $this->setModel($group);

        return true;
    }

    /**
     * @throws NotSupportedException
     */
    public function getAuthor(): MemberRepositoryInterface
    {
        throw new NotSupportedException('Group does not have author!');
    }
}
