<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Repositories;

use DomainException;
use LogicException;
use Podium\ActiveRecordApi\ActiveRecords\MessageParticipantActiveRecord;
use Podium\ActiveRecordApi\Enums\MessageSide;
use Podium\ActiveRecordApi\Enums\MessageStatus;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MessageParticipantRepositoryInterface;
use Podium\Api\Interfaces\MessageRepositoryInterface;
use Throwable;
use yii\base\NotSupportedException;
use yii\data\ActiveDataProvider;
use yii\data\DataFilter;
use yii\data\Pagination;
use yii\data\Sort;
use yii\db\StaleObjectException;

use function is_int;
use function is_string;

final class MessageParticipantRepository implements MessageParticipantRepositoryInterface
{
    public string $activeRecordClass = MessageParticipantActiveRecord::class;

    private ?MessageParticipantActiveRecord $model = null;

    private array $errors = [];

    private ?ActiveDataProvider $collection = null;

    public function getModel(): MessageParticipantActiveRecord
    {
        if (null === $this->model) {
            throw new LogicException('You need to call fetchOne() or setModel() first!');
        }

        return $this->model;
    }

    public function setModel(MessageParticipantActiveRecord $activeRecord): void
    {
        $this->model = $activeRecord;
    }

    public function getCollection(): ?ActiveDataProvider
    {
        return $this->collection;
    }

    public function setCollection(?ActiveDataProvider $collection): void
    {
        $this->collection = $collection;
    }

    public function fetchOne(MessageRepositoryInterface $message, MemberRepositoryInterface $member): bool
    {
        $messageId = $message->getId();
        if (!is_int($messageId)) {
            throw new DomainException('Invalid message ID!');
        }
        $memberId = $member->getId();
        if (!is_int($memberId)) {
            throw new DomainException('Invalid member ID!');
        }

        $modelClass = $this->activeRecordClass;

        /** @var MessageParticipantActiveRecord $modelClass */
        $model = $modelClass::find()->where(
            [
                'message_id' => $messageId,
                'member_id' => $memberId,
            ]
        )->one();

        if (null === $model) {
            return false;
        }

        $this->setModel($model);

        return true;
    }

    /**
     * @param DataFilter|null            $filter
     * @param bool|array|Sort|null       $sort
     * @param bool|array|Pagination|null $pagination
     *
     * @throws NotSupportedException
     */
    public function fetchAll($filter = null, $sort = null, $pagination = null): void
    {
        $modelClass = $this->activeRecordClass;

        /** @var MessageParticipantActiveRecord $modelClass */
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

    public function edit(array $data = []): bool
    {
        $model = $this->getModel();

        if (!$model->load($data, '')) {
            return false;
        }

        if (!$model->validate()) {
            $this->errors = $model->errors;

            return false;
        }

        return $model->save(false);
    }

    public function getParent(): MessageRepositoryInterface
    {
        $message = $this->getModel()->message;

        $parent = new MessageRepository();
        $parent->setModel($message);

        return $parent;
    }

    public function isArchived(): bool
    {
        return $this->getModel()->archived;
    }

    public function archive(): bool
    {
        $messageSide = $this->getModel();

        $messageSide->archived = true;

        if (!$messageSide->validate()) {
            $this->errors = $messageSide->errors;

            return false;
        }

        return $messageSide->save(false);
    }

    public function revive(): bool
    {
        $messageSide = $this->getModel();

        $messageSide->archived = false;

        if (!$messageSide->validate()) {
            $this->errors = $messageSide->errors;

            return false;
        }

        return $messageSide->save(false);
    }

    /**
     * @param string $sideId
     */
    public function copy(
        MessageRepositoryInterface $message,
        MemberRepositoryInterface $member,
        $sideId,
        array $data = []
    ): bool {
        $messageId = $message->getId();
        if (!is_int($messageId)) {
            throw new DomainException('Invalid message ID!');
        }
        $memberId = $member->getId();
        if (!is_int($memberId)) {
            throw new DomainException('Invalid member ID!');
        }
        if (!is_string($sideId)) {
            throw new DomainException('Invalid side ID!');
        }

        /** @var MessageParticipantActiveRecord $model */
        $model = new $this->activeRecordClass();

        if (!$model->load($data, '')) {
            return false;
        }

        $model->message_id = $messageId;
        $model->member_id = $memberId;
        $model->archived = false;
        $model->side_id = $sideId;
        $model->status_id = MessageSide::SENDER === $sideId ? MessageStatus::READ : MessageStatus::NEW;

        if (!$model->save()) {
            $this->errors = $model->errors;

            return false;
        }

        $this->setModel($model);

        return true;
    }
}
