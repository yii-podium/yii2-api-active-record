<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Repositories;

use DomainException;
use LogicException;
use Podium\ActiveRecordApi\ActiveRecords\ThumbActiveRecord;
use Podium\ActiveRecordApi\Enums\Thumb;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\ThumbRepositoryInterface;
use Throwable;
use yii\db\StaleObjectException;

use function is_int;

final class ThumbRepository implements ThumbRepositoryInterface
{
    public string $activeRecordClass = ThumbActiveRecord::class;

    private ?ThumbActiveRecord $model = null;

    private array $errors = [];

    public function getModel(): ThumbActiveRecord
    {
        if (null === $this->model) {
            throw new LogicException('You need to call fetchOne() or setModel() first!');
        }

        return $this->model;
    }

    public function setModel(ThumbActiveRecord $activeRecord): void
    {
        $this->model = $activeRecord;
    }

    public function prepare(MemberRepositoryInterface $member, PostRepositoryInterface $post): void
    {
        $memberId = $member->getId();
        if (!is_int($memberId)) {
            throw new DomainException('Invalid member ID!');
        }
        $postId = $post->getId();
        if (!is_int($postId)) {
            throw new DomainException('Invalid post ID!');
        }

        /** @var ThumbActiveRecord $thumb */
        $thumb = new $this->activeRecordClass();

        $thumb->member_id = $memberId;
        $thumb->post_id = $postId;
        $thumb->thumb = Thumb::NONE;

        $this->model = $thumb;
    }

    public function fetchOne(MemberRepositoryInterface $member, PostRepositoryInterface $post): bool
    {
        $memberId = $member->getId();
        if (!is_int($memberId)) {
            throw new DomainException('Invalid member ID!');
        }
        $postId = $post->getId();
        if (!is_int($postId)) {
            throw new DomainException('Invalid post ID!');
        }

        /** @var ThumbActiveRecord $modelClass */
        $modelClass = $this->activeRecordClass;

        /** @var ThumbActiveRecord|null $model */
        $model = $modelClass::find()->where(
            [
                'member_id' => $memberId,
                'post_id' => $postId,
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
    public function reset(): bool
    {
        return is_int($this->getModel()->delete());
    }

    public function isUp(): bool
    {
        return Thumb::UP === $this->getModel()->thumb;
    }

    public function isDown(): bool
    {
        return Thumb::DOWN === $this->getModel()->thumb;
    }

    public function up(): bool
    {
        $thumb = $this->getModel();

        $thumb->thumb = Thumb::UP;

        if (!$thumb->validate()) {
            $this->errors = $thumb->errors;

            return false;
        }

        return $thumb->save(false);
    }

    public function down(): bool
    {
        $thumb = $this->getModel();

        $thumb->thumb = Thumb::DOWN;

        if (!$thumb->validate()) {
            $this->errors = $thumb->errors;
        }

        return $thumb->save(false);
    }
}
