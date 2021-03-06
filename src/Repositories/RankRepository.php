<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Repositories;

use LogicException;
use Podium\ActiveRecordApi\ActiveRecords\RankActiveRecord;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RankRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use yii\base\NotSupportedException;

final class RankRepository implements RankRepositoryInterface
{
    use ActiveRecordRepositoryTrait;

    public string $activeRecordClass = RankActiveRecord::class;

    private ?RankActiveRecord $model = null;

    public function getActiveRecordClass(): string
    {
        return $this->activeRecordClass;
    }

    public function getModel(): RankActiveRecord
    {
        if (null === $this->model) {
            throw new LogicException('You need to call fetchOne() or setModel() first!');
        }

        return $this->model;
    }

    public function setModel(RankActiveRecord $model): void
    {
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
        throw new NotSupportedException('Rank does not have parent!');
    }

    public function create(array $data = []): bool
    {
        /** @var RankActiveRecord $rank */
        $rank = new $this->activeRecordClass();

        if (!$rank->load($data, '')) {
            return false;
        }

        if (!$rank->save()) {
            $this->errors = $rank->errors;

            return false;
        }

        $this->setModel($rank);

        return true;
    }

    /**
     * @throws NotSupportedException
     */
    public function getAuthor(): MemberRepositoryInterface
    {
        throw new NotSupportedException('Rank does not have author!');
    }
}
