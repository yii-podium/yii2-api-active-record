<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Repositories;

use DomainException;
use LogicException;
use Podium\ActiveRecordApi\ActiveRecords\PostActiveRecord;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use yii\db\ActiveRecord;

use function is_int;

final class PostRepository implements PostRepositoryInterface
{
    use ActiveRecordRepositoryTrait;

    public string $activeRecordClass = PostActiveRecord::class;

    private ?PostActiveRecord $model = null;

    public function getActiveRecordClass(): string
    {
        return $this->activeRecordClass;
    }

    public function getModel(): PostActiveRecord
    {
        if (null === $this->model) {
            throw new LogicException('You need to call fetchOne() or setModel() first!');
        }

        return $this->model;
    }

    public function setModel(ActiveRecord $postModel): void
    {
        if (!$postModel instanceof PostActiveRecord) {
            throw new LogicException('You need to pass Podium\ActiveRecordApi\ActiveRecords\PostActiveRecord!');
        }

        $this->model = $postModel;
    }

    public function getId(): int
    {
        return $this->getModel()->id;
    }

    public function getParent(): RepositoryInterface
    {
        $threadRepository = $this->getModel()->thread;

        $parent = new ThreadRepository();
        $parent->setModel($threadRepository);

        return $parent;
    }

    public function isArchived(): bool
    {
        return $this->getModel()->archived;
    }

    public function getCreatedAt(): int
    {
        return $this->getModel()->created_at;
    }

    public function create(
        MemberRepositoryInterface $author,
        ThreadRepositoryInterface $thread,
        array $data = []
    ): bool {
        $authorId = $author->getId();
        if (!is_int($authorId)) {
            throw new DomainException('Invalid author ID!');
        }
        $threadId = $thread->getId();
        if (!is_int($threadId)) {
            throw new DomainException('Invalid thread ID!');
        }

        /** @var PostActiveRecord $post */
        $post = new $this->activeRecordClass();

        if (!$post->load($data, '')) {
            return false;
        }

        $post->author_id = $authorId;
        $post->thread_id = $threadId;

        if (!$post->save()) {
            $this->errors = $post->errors;

            return false;
        }

        $this->setModel($post);

        return true;
    }

    public function move(ThreadRepositoryInterface $thread): bool
    {
        $threadId = $thread->getId();
        if (!is_int($threadId)) {
            throw new DomainException('Invalid thread ID!');
        }

        $post = $this->getModel();

        $post->thread_id = $threadId;

        if (!$post->validate()) {
            $this->errors = $post->errors;

            return false;
        }

        return $post->save(false);
    }

    public function archive(): bool
    {
        $post = $this->getModel();

        $post->archived = true;

        if (!$post->validate()) {
            $this->errors = $post->errors;

            return false;
        }

        return $post->save(false);
    }

    public function revive(): bool
    {
        $post = $this->getModel();

        $post->archived = false;

        if (!$post->validate()) {
            $this->errors = $post->errors;

            return false;
        }

        return $post->save(false);
    }

    public function updateCounters(int $likes, int $dislikes): bool
    {
        return $this->getModel()->updateCounters(
            [
                'likes' => $likes,
                'dislikes' => $dislikes,
            ]
        );
    }

    public function pin(): bool
    {
        $post = $this->getModel();

        $post->pinned = true;

        if (!$post->validate()) {
            $this->errors = $post->errors;

            return false;
        }

        return $post->save(false);
    }

    public function unpin(): bool
    {
        $post = $this->getModel();

        $post->pinned = false;

        if (!$post->validate()) {
            $this->errors = $post->errors;

            return false;
        }

        return $post->save(false);
    }

    public function getAuthor(): MemberRepositoryInterface
    {
        $member = new MemberRepository();
        $member->setModel($this->getModel()->author);

        return $member;
    }
}
