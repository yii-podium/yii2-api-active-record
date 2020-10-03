<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\repositories;

use Podium\ActiveRecordApi\ActiveRecords\RankActiveRecord;
use Podium\ActiveRecordApi\interfaces\RankRepositoryInterface;
use Podium\ActiveRecordApi\interfaces\RepositoryInterface;
use LogicException;
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

    public function setModel(?RankActiveRecord $activeRecord): void
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
}
