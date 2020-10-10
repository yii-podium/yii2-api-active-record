<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Repositories;

use LogicException;
use Podium\ActiveRecordApi\ActiveRecords\MemberActiveRecord;
use Podium\ActiveRecordApi\Enums\MemberStatus;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use yii\base\NotSupportedException;
use yii\helpers\Json;

final class MemberRepository implements MemberRepositoryInterface
{
    use ActiveRecordRepositoryTrait;

    public string $activeRecordClass = MemberActiveRecord::class;

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

    public function isIgnoring(MemberRepositoryInterface $member): bool
    {
        // TODO: Implement isIgnoring() method.
    }
}
