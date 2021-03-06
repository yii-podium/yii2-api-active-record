<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Repositories;

use DomainException;
use LogicException;
use Podium\ActiveRecordApi\ActiveRecords\AcquaintanceActiveRecord;
use Podium\ActiveRecordApi\Enums\AcquaintanceType;
use Podium\Api\Interfaces\AcquaintanceRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Throwable;
use yii\db\StaleObjectException;

use function is_int;

final class AcquaintanceRepository implements AcquaintanceRepositoryInterface
{
    public string $activeRecordClass = AcquaintanceActiveRecord::class;

    private ?AcquaintanceActiveRecord $model = null;

    private array $errors = [];

    public function getModel(): AcquaintanceActiveRecord
    {
        if (null === $this->model) {
            throw new LogicException('You need to call fetchOne() or setModel() first!');
        }

        return $this->model;
    }

    public function setModel(AcquaintanceActiveRecord $activeRecord): void
    {
        $this->model = $activeRecord;
    }

    public function fetchOne(MemberRepositoryInterface $member, MemberRepositoryInterface $target): bool
    {
        $memberId = $member->getId();
        if (!is_int($memberId)) {
            throw new DomainException('Invalid member ID!');
        }
        $targetId = $target->getId();
        if (!is_int($targetId)) {
            throw new DomainException('Invalid target ID!');
        }

        /** @var AcquaintanceActiveRecord $modelClass */
        $modelClass = $this->activeRecordClass;
        /** @var AcquaintanceActiveRecord|null $model */
        $model = $modelClass::find()->where(
            [
                'member_id' => $memberId,
                'target_id' => $targetId,
            ]
        )->one();

        if (null === $model) {
            return false;
        }

        $this->setModel($model);

        return true;
    }

    public function prepare(MemberRepositoryInterface $member, MemberRepositoryInterface $target): void
    {
        $memberId = $member->getId();
        if (!is_int($memberId)) {
            throw new DomainException('Invalid member ID!');
        }
        $targetId = $target->getId();
        if (!is_int($targetId)) {
            throw new DomainException('Invalid target ID!');
        }

        /** @var AcquaintanceActiveRecord $acquaintance */
        $acquaintance = new $this->activeRecordClass();

        $acquaintance->member_id = $memberId;
        $acquaintance->target_id = $targetId;

        $this->setModel($acquaintance);
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

    public function befriend(): bool
    {
        $acquaintance = $this->getModel();

        $acquaintance->type_id = AcquaintanceType::FRIEND;

        if (!$acquaintance->validate()) {
            $this->errors = $acquaintance->errors;

            return false;
        }

        return $acquaintance->save(false);
    }

    public function ignore(): bool
    {
        $acquaintance = $this->getModel();

        $acquaintance->type_id = AcquaintanceType::IGNORE;

        if (!$acquaintance->validate()) {
            $this->errors = $acquaintance->errors;

            return false;
        }

        return $acquaintance->save(false);
    }

    public function isFriend(): bool
    {
        return AcquaintanceType::FRIEND === $this->getModel()->type_id;
    }

    public function isIgnoring(): bool
    {
        return AcquaintanceType::IGNORE === $this->getModel()->type_id;
    }
}
