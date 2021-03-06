<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Repositories;

use DomainException;
use LogicException;
use Podium\ActiveRecordApi\ActiveRecords\BookmarkActiveRecord;
use Podium\Api\Interfaces\BookmarkRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;

use function is_int;

final class BookmarkRepository implements BookmarkRepositoryInterface
{
    public string $activeRecordClass = BookmarkActiveRecord::class;

    private array $errors = [];

    private ?BookmarkActiveRecord $model = null;

    public function getModel(): BookmarkActiveRecord
    {
        if (null === $this->model) {
            throw new LogicException('You need to call fetchOne() or setModel() first!');
        }

        return $this->model;
    }

    public function setModel(BookmarkActiveRecord $activeRecord): void
    {
        $this->model = $activeRecord;
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

        /** @var BookmarkActiveRecord $modelClass */
        $modelClass = $this->activeRecordClass;

        /** @var BookmarkActiveRecord|null $model */
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

    public function prepare(MemberRepositoryInterface $member, ThreadRepositoryInterface $thread): void
    {
        $memberId = $member->getId();
        if (!is_int($memberId)) {
            throw new DomainException('Invalid member ID!');
        }
        $threadId = $thread->getId();
        if (!is_int($threadId)) {
            throw new DomainException('Invalid thread ID!');
        }

        /** @var BookmarkActiveRecord $model */
        $model = new $this->activeRecordClass();

        $model->member_id = $memberId;
        $model->thread_id = $threadId;

        $this->setModel($model);
    }

    public function getLastSeen(): ?int
    {
        return $this->getModel()->last_seen;
    }

    public function mark(int $timeMark): bool
    {
        $bookmark = $this->getModel();

        $bookmark->last_seen = $timeMark;

        if (!$bookmark->save()) {
            $this->errors = $bookmark->errors;

            return false;
        }

        $this->setModel($bookmark);

        return true;
    }
}
