<?php

declare(strict_types=1);

namespace Podium\ActiveRecordApi\Repositories;

use DomainException;
use LogicException;
use Podium\ActiveRecordApi\ActiveRecords\ForumActiveRecord;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use yii\db\ActiveRecord;

use function is_int;

final class ForumRepository implements ForumRepositoryInterface
{
    use ActiveRecordRepositoryTrait;

    public string $activeRecordClass = ForumActiveRecord::class;

    private ?ForumActiveRecord $model = null;

    public function getActiveRecordClass(): string
    {
        return $this->activeRecordClass;
    }

    public function getModel(): ForumActiveRecord
    {
        if (null === $this->model) {
            throw new LogicException('You need to call fetchOne() or setModel() first!');
        }

        return $this->model;
    }

    public function setModel(?ActiveRecord $model): void
    {
        if (!$model instanceof ForumActiveRecord) {
            throw new LogicException('You need to pass Podium\ActiveRecordApi\ActiveRecords\ForumActiveRecord!');
        }

        $this->model = $model;
    }

    public function getId(): int
    {
        return $this->getModel()->id;
    }

    public function getParent(): RepositoryInterface
    {
        $category = $this->getModel()->category;

        $parent = new CategoryRepository();
        $parent->setModel($category);

        return $parent;
    }

    public function isArchived(): bool
    {
        return (bool) $this->getModel()->archived;
    }

    public function create(
        MemberRepositoryInterface $author,
        CategoryRepositoryInterface $category,
        array $data = []
    ): bool {
        $authorId = $author->getId();
        if (!is_int($authorId)) {
            throw new DomainException('Invalid author ID!');
        }
        $categoryId = $category->getId();
        if (!is_int($categoryId)) {
            throw new DomainException('Invalid category ID!');
        }

        /** @var ForumActiveRecord $forum */
        $forum = new $this->activeRecordClass();

        if (!$forum->load($data, '')) {
            return false;
        }

        if (null === $forum->sort) {
            /** @var ForumActiveRecord $forumClass */
            $forumClass = $this->activeRecordClass;

            /** @var ForumActiveRecord|null $lastForum */
            $lastForum = $forumClass::find()->orderBy(
                [
                    'sort' => SORT_DESC,
                    'name' => SORT_DESC,
                ]
            )->limit(1)->one();

            if ($lastForum) {
                $forum->sort = $lastForum->sort + 1;
            } else {
                $forum->sort = 0;
            }
        }

        $forum->author_id = $authorId;
        $forum->category_id = $categoryId;

        if (!$forum->save()) {
            $this->errors = $forum->errors;

            return false;
        }

        $this->setModel($forum);

        return true;
    }

    public function archive(): bool
    {
        $forum = $this->getModel();

        $forum->archived = true;

        if (!$forum->validate()) {
            $this->errors = $forum->errors;

            return false;
        }

        return $forum->save(false);
    }

    public function revive(): bool
    {
        $forum = $this->getModel();

        $forum->archived = false;

        if (!$forum->validate()) {
            $this->errors = $forum->errors;

            return false;
        }

        return $forum->save(false);
    }

    public function updateCounters(int $threads, int $posts): bool
    {
        return $this->getModel()->updateCounters(
            [
                'threads_count' => $threads,
                'posts_count' => $posts,
            ]
        );
    }

    public function setOrder(int $order): bool
    {
        $forum = $this->getModel();

        $forum->sort = $order;

        if (!$forum->validate()) {
            $this->errors = $forum->errors;

            return false;
        }

        return $forum->save(false);
    }

    public function getOrder(): int
    {
        return $this->getModel()->sort;
    }

    public function sort(): bool
    {
        /** @var ForumActiveRecord $forumClass */
        $forumClass = $this->activeRecordClass;

        $forums = $forumClass::find()->orderBy(
            [
                'sort' => SORT_ASC,
                'name' => SORT_ASC,
            ]
        );

        $sortOrder = 0;

        /** @var ForumActiveRecord $forum */
        foreach ($forums->each() as $forum) {
            $forum->sort = $sortOrder++;

            if (!$forum->save()) {
                return false;
            }
        }

        return true;
    }

    public function move(CategoryRepositoryInterface $category): bool
    {
        $categoryId = $category->getId();
        if (!is_int($categoryId)) {
            throw new DomainException('Invalid category ID!');
        }

        $forum = $this->getModel();

        $forum->category_id = $categoryId;

        if (!$forum->validate()) {
            $this->errors = $forum->errors;

            return false;
        }

        return $forum->save(false);
    }

    public function isHidden(): bool
    {
        return !(bool) $this->getModel()->visible;
    }

    public function hide(): bool
    {
        $forum = $this->getModel();

        $forum->visible = false;

        if (!$forum->validate()) {
            $this->errors = $forum->errors;

            return false;
        }

        return $forum->save(false);
    }

    public function reveal(): bool
    {
        $forum = $this->getModel();

        $forum->visible = true;

        if (!$forum->validate()) {
            $this->errors = $forum->errors;

            return false;
        }

        return $forum->save(false);
    }

    public function getAuthor(): MemberRepositoryInterface
    {
        $member = new MemberRepository();
        $member->setModel($this->getModel()->author);

        return $member;
    }
}
