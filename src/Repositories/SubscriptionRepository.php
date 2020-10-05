<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Repositories;

use DomainException;
use LogicException;
use Podium\ActiveRecordApi\ActiveRecords\SubscriptionActiveRecord;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\SubscriptionRepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Throwable;
use yii\db\StaleObjectException;

use function is_int;

final class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    public string $activeRecordClass = SubscriptionActiveRecord::class;

    private ?SubscriptionActiveRecord $model = null;

    private array $errors = [];

    public function getModel(): SubscriptionActiveRecord
    {
        if (null === $this->model) {
            throw new LogicException('You need to call fetchOne() or setModel() first!');
        }

        return $this->model;
    }

    public function setModel(SubscriptionActiveRecord $activeRecord): void
    {
        $this->model = $activeRecord;
    }

    public function isMemberSubscribed(MemberRepositoryInterface $member, ThreadRepositoryInterface $thread): bool
    {
        $memberId = $member->getId();
        if (!is_int($memberId)) {
            throw new DomainException('Invalid member ID!');
        }
        $threadId = $thread->getId();
        if (!is_int($threadId)) {
            throw new DomainException('Invalid thread ID!');
        }

        $modelClass = $this->activeRecordClass;

        /* @var SubscriptionActiveRecord $modelClass */
        return $modelClass::find()->where(
            [
                'member_id' => $memberId,
                'thread_id' => $threadId,
            ]
        )->exists();
    }

    public function subscribe(MemberRepositoryInterface $member, ThreadRepositoryInterface $thread): bool
    {
        $memberId = $member->getId();
        if (!is_int($memberId)) {
            throw new DomainException('Invalid member ID!');
        }
        $threadId = $thread->getId();
        if (!is_int($threadId)) {
            throw new DomainException('Invalid thread ID!');
        }

        /** @var SubscriptionActiveRecord $model */
        $model = new $this->activeRecordClass();

        $model->member_id = $memberId;
        $model->thread_id = $threadId;

        if (!$model->save()) {
            $this->errors = $model->errors;

            return false;
        }

        $this->setModel($model);

        return true;
    }

    public function fetchOne(MemberRepositoryInterface $member, ThreadRepositoryInterface $thread): bool
    {
        $memberId = $member->getId();
        if (!is_int($memberId)) {
            throw new DomainException('Invalid member ID!');
        }
        $threadId = $thread->getId();
        if (!is_int($threadId)) {
            throw new DomainException('Invalid thread ID!');
        }

        /** @var SubscriptionActiveRecord $modelClass */
        $modelClass = $this->activeRecordClass;

        /** @var SubscriptionActiveRecord|null $model */
        $model = $modelClass::find()->where(
            [
                'member_id' => $memberId,
                'thread_id' => $threadId,
            ]
        )->one();

        if (null === $model) {
            return false;
        }

        $this->setModel($model);

        return true;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function delete(): bool
    {
        return is_int($this->getModel()->delete());
    }
}
