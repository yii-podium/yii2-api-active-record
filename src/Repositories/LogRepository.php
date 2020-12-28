<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Repositories;

use DomainException;
use LogicException;
use Podium\ActiveRecordApi\ActiveRecords\LogActiveRecord;
use Podium\Api\Interfaces\LogRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Throwable;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

use function is_int;

final class LogRepository implements LogRepositoryInterface
{
    use ActiveRecordRepositoryTrait;

    public string $activeRecordClass = LogActiveRecord::class;

    private ?LogActiveRecord $model = null;

    public function getActiveRecordClass(): string
    {
        return $this->activeRecordClass;
    }

    public function getModel(): LogActiveRecord
    {
        if (null === $this->model) {
            throw new LogicException('You need to call fetchOne() or setModel() first!');
        }

        return $this->model;
    }

    public function setModel(?ActiveRecord $model): void
    {
        if (!$model instanceof LogActiveRecord) {
            throw new LogicException('You need to pass Podium\ActiveRecordApi\ActiveRecords\LogActiveRecord!');
        }

        $this->model = $model;
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

    public function create(MemberRepositoryInterface $author, string $action, array $data = []): bool
    {
        $authorId = $author->getId();
        if (!is_int($authorId)) {
            throw new DomainException('Invalid author ID!');
        }

        /** @var LogActiveRecord $log */
        $log = new $this->activeRecordClass();

        if ([] !== $data && !$log->load($data, '')) {
            return false;
        }

        $log->action = $action;
        $log->member_id = $authorId;

        if (!$log->save()) {
            $this->errors = $log->errors;

            return false;
        }

        $this->setModel($log);

        return true;
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
        throw new NotSupportedException('Log does not have parent!');
    }

    /**
     * @throws NotSupportedException
     */
    public function edit(array $data = []): bool
    {
        throw new NotSupportedException('Log does not support editing!');
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
