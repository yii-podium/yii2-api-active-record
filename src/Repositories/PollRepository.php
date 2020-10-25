<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Repositories;

use DomainException;
use Exception;
use LogicException;
use Podium\ActiveRecordApi\ActiveRecords\PollActiveRecord;
use Podium\ActiveRecordApi\Enums\PollAnswerAction;
use Podium\ActiveRecordApi\Enums\PollChoice;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\PollAnswerRepositoryInterface;
use Podium\Api\Interfaces\PollRepositoryInterface;
use Podium\Api\Interfaces\PollVoteRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use yii\db\ActiveRecord;

use function is_int;
use function is_string;

final class PollRepository implements PollRepositoryInterface
{
    use ActiveRecordRepositoryTrait;

    public string $activeRecordClass = PollActiveRecord::class;

    private ?PollActiveRecord $model = null;

    public function getActiveRecordClass(): string
    {
        return $this->activeRecordClass;
    }

    public function getModel(): PollActiveRecord
    {
        if (null === $this->model) {
            throw new LogicException('You need to call fetchOne() or setModel() first!');
        }

        return $this->model;
    }

    public function setModel(ActiveRecord $pollActiveRecord): void
    {
        if (!$pollActiveRecord instanceof PollActiveRecord) {
            throw new LogicException('You need to pass Podium\ActiveRecordApi\ActiveRecords\PollActiveRecord!');
        }

        $this->model = $pollActiveRecord;
    }

    public function getId(): int
    {
        return $this->getModel()->id;
    }

    public function getParent(): RepositoryInterface
    {
        $postModel = $this->getModel()->post;

        $parent = new PostRepository();
        $parent->setModel($postModel);

        return $parent;
    }

    public function create(array $data, array $answers = []): bool
    {

        /** @var PollActiveRecord $poll */
        $poll = new $this->activeRecordClass();
        if (!$poll->load($data, '')) {
            return false;
        }

        $poll->author_id = $authorId;
        $poll->thread_id = $threadId;

        if (!$poll->save()) {
            $this->errors = $poll->errors;

            return false;
        }

        $this->setModel($poll);

        $answerRepository = $this->getAnswerRepository();
        foreach ($answers as $answer) {
            if (!$answerRepository->create($answer)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function edit(array $answers = [], array $data = []): bool
    {
        $model = $this->getModel();
        if (!$model->load($data, '')) {
            return false;
        }

        if (!$model->save()) {
            $this->errors = $model->errors;

            return false;
        }

        $answerRepository = $this->getAnswerRepository();
        foreach ($answers as $answer) {
            $id = null;
            if (is_string($answer)) {
                $text = $answer;
                $action = PollAnswerAction::ADD;
            } else {
                $text = $answer[0] ?? null;
                $action = $answer['action'] ?? PollAnswerAction::ADD;
                $id = $answer['id'] ?? null;
            }

            switch ($action) {
                case PollAnswerAction::EDIT:
                    $actionResult = $answerRepository->edit($id, $text);
                    break;

                case PollAnswerAction::REMOVE:
                    $actionResult = $answerRepository->remove($id);
                    break;

                case PollAnswerAction::ADD:
                default:
                    $actionResult = $answerRepository->create($text);
                    break;
            }
            if (!$actionResult) {
                return false;
            }
        }

        return true;
    }

    public function move(ThreadRepositoryInterface $thread): bool
    {
        $threadId = $thread->getId();
        if (!is_int($threadId)) {
            throw new DomainException('Invalid thread ID!');
        }

        $poll = $this->getModel();

        $poll->thread_id = $threadId;

        if (!$poll->validate()) {
            $this->errors = $poll->errors;

            return false;
        }

        return $poll->save(false);
    }

    private ?PollAnswerRepositoryInterface $pollAnswerRepository = null;

    public function getAnswerRepository(): PollAnswerRepositoryInterface
    {
        if (null === $this->pollAnswerRepository) {
            $this->pollAnswerRepository = new PollAnswerRepository($this);
        }

        return $this->pollAnswerRepository;
    }

    private ?PollVoteRepositoryInterface $pollVoteRepository = null;

    public function getVoteRepository(): PollVoteRepositoryInterface
    {
        if (null === $this->pollVoteRepository) {
            $this->pollVoteRepository = new PollVoteRepository($this);
        }

        return $this->pollVoteRepository;
    }

    public function hasMemberVoted(MemberRepositoryInterface $member): bool
    {
        return $this->getVoteRepository()->hasMemberVoted($member);
    }

    public function isSingleChoice(): bool
    {
        return PollChoice::SINGLE === $this->getModel()->choice_id;
    }

    public function vote(MemberRepositoryInterface $member, array $answers): bool
    {
        foreach ($answers as $answerId) {
            if (!$this->getAnswerRepository()->isAnswer($answerId)) {
                throw new LogicException('Provided Poll Answer does not belong to the voted Poll!');
            }

            $pollVoteRepository = $this->getVoteRepository();
            if (!$pollVoteRepository->register($member, $answerId)) {
                $this->errors = $pollVoteRepository->getErrors();

                return false;
            }
        }

        return true;
    }

    public function pin(): bool
    {
        $poll = $this->getModel();
        $poll->pinned = true;
        if (!$poll->validate()) {
            $this->errors = $poll->errors;

            return false;
        }

        return $poll->save(false);
    }

    public function unpin(): bool
    {
        $poll = $this->getModel();
        $poll->pinned = false;
        if (!$poll->validate()) {
            $this->errors = $poll->errors;

            return false;
        }

        return $poll->save(false);
    }

    public function areAnswersAcceptable(array $answers): bool
    {
        // TODO: Implement areAnswersAcceptable() method.
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
