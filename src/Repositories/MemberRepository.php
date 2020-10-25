<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Repositories;

use LogicException;
use Podium\ActiveRecordApi\ActiveRecords\MemberActiveRecord;
use Podium\ActiveRecordApi\Enums\MemberStatus;
use Podium\Api\Interfaces\AcquaintanceRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\RoleRepositoryInterface;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\di\Instance;
use yii\helpers\Json;

final class MemberRepository implements MemberRepositoryInterface
{
    use ActiveRecordRepositoryTrait;

    public string $activeRecordClass = MemberActiveRecord::class;

    public string $acquaintanceRepositoryConfig = AcquaintanceRepository::class;

    private ?MemberActiveRecord $model = null;

    public function getActiveRecordClass(): string
    {
        return $this->activeRecordClass;
    }

    public function getModel(): MemberActiveRecord
    {
        if (null === $this->model) {
            throw new LogicException('You need to call fetchOne() or setModel() first!');
        }

        return $this->model;
    }

    public function setModel(MemberActiveRecord $activeRecord): void
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
        throw new NotSupportedException('Member does not have parent!');
    }

    /**
     * @param int|string|array $id
     */
    public function register($id, array $data = []): bool
    {
        /** @var MemberActiveRecord $member */
        $member = new $this->activeRecordClass();

        if ([] !== $data && !$member->load($data, '')) {
            return false;
        }

        $member->user_id = Json::encode($id);
        $member->status_id = MemberStatus::ACTIVE;

        if (!$member->save()) {
            $this->errors = $member->errors;

            return false;
        }

        $this->setModel($member);

        return true;
    }

    public function ban(): bool
    {
        $member = $this->getModel();

        $member->status_id = MemberStatus::BANNED;

        if (!$member->validate()) {
            $this->errors = $member->errors;

            return false;
        }

        return $member->save(false);
    }

    public function unban(): bool
    {
        $member = $this->getModel();

        $member->status_id = MemberStatus::ACTIVE;

        if (!$member->validate()) {
            $this->errors = $member->errors;

            return false;
        }

        return $member->save(false);
    }

    public function isBanned(): bool
    {
        return MemberStatus::BANNED === $this->getModel()->status_id;
    }

    private ?AcquaintanceRepositoryInterface $acquaintanceRepository = null;

    /**
     * @throws InvalidConfigException
     */
    public function getAcquaintanceRepository(): AcquaintanceRepositoryInterface
    {
        if (null === $this->acquaintanceRepository) {
            /** @var AcquaintanceRepositoryInterface $repository */
            $repository = Instance::ensure(
                $this->acquaintanceRepositoryConfig,
                AcquaintanceRepositoryInterface::class
            );
            $this->acquaintanceRepository = $repository;
        }

        return $this->acquaintanceRepository;
    }

    /**
     * @throws InvalidConfigException
     */
    public function isIgnoring(MemberRepositoryInterface $target): bool
    {
        $acquaintance = $this->getAcquaintanceRepository();
        if (!$acquaintance->fetchOne($this, $target)) {
            return false;
        }

        return $acquaintance->isIgnoring();
    }

    public function isGroupMember(array $groups): bool
    {
        // TODO: Implement isGroupMember() method.
    }

    public function hasRole(RepositoryInterface $subject = null, string $type = null): bool
    {
        // TODO: Implement hasRole() method.
    }

    public function addRole(RoleRepositoryInterface $role): bool
    {
        // TODO: Implement addRole() method.
    }

    public function removeRole(RoleRepositoryInterface $role): bool
    {
        // TODO: Implement removeRole() method.
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
