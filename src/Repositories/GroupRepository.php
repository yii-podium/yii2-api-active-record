<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Repositories;

use LogicException;
use Podium\ActiveRecordApi\ActiveRecords\GroupActiveRecord;
use Podium\Api\Interfaces\GroupMemberRepositoryInterface;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use yii\base\NotSupportedException;
use yii\di\Instance;

final class GroupRepository implements GroupRepositoryInterface
{
    use ActiveRecordRepositoryTrait;

    public string $activeRecordClass = GroupActiveRecord::class;

    /**
     * @var string|array|GroupMemberRepositoryInterface
     */
    public $groupMemberRepositoryConfig = GroupMemberRepository::class;

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

    public function setModel(GroupActiveRecord $activeRecord): void
    {
        $this->model = $activeRecord;
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

    private ?GroupMemberRepositoryInterface $groupMemberRepository = null;

    public function getGroupMember(): GroupMemberRepositoryInterface
    {
        if (null === $this->groupMemberRepository) {
            /** @var GroupMemberRepositoryInterface $repository */
            $repository = Instance::ensure($this->groupMemberRepositoryConfig, GroupMemberRepositoryInterface::class);
            $this->groupMemberRepository = $repository;
        }

        return $this->groupMemberRepository;
    }

    public function getAuthor(): MemberRepositoryInterface
    {
        // TODO: Implement getAuthor() method.
    }

    public function getAllowedGroups(): array
    {
        // TODO: Implement getAllowedGroups() method.
    }
}
