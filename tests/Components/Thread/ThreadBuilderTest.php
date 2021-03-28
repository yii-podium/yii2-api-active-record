<?php

declare(strict_types=1);

namespace Podium\Tests\Components\Thread;

use Podium\ActiveRecordApi\ActiveRecords\ForumActiveRecord;
use Podium\ActiveRecordApi\ActiveRecords\MemberActiveRecord;
use Podium\ActiveRecordApi\ActiveRecords\ThreadActiveRecord;
use Podium\ActiveRecordApi\Repositories\ForumRepository;
use Podium\ActiveRecordApi\Repositories\MemberRepository;
use Podium\ActiveRecordApi\Repositories\ThreadRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\ThreadFixture;

class ThreadBuilderTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [ThreadFixture::class];
    }

    public function testCreatingWithMinimalData(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $forum = new ForumRepository();
        $forum->setModel(ForumActiveRecord::findOne(1));

        $response = $this->podium->thread->create($author, $forum, ['name' => 'New Thread']);

        self::assertTrue($response->getResult());

        $thread = ThreadActiveRecord::findOne(4);
        self::assertSame(1, $thread->author_id);
        self::assertSame(1, $thread->author->id);
        self::assertSame(0, $thread->locked);
        self::assertSame(0, $thread->pinned);
        self::assertSame('New Thread', $thread->name);
        self::assertSame('new-thread', $thread->slug);
        self::assertSame(0, $thread->archived);
        self::assertNull($thread->created_post_at);
        self::assertNull($thread->updated_post_at);
        self::assertEqualsWithDelta(time(), $thread->created_at, 10);
        self::assertEqualsWithDelta(time(), $thread->updated_at, 10);
    }

    public function testCreatingWithFullData(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $forum = new ForumRepository();
        $forum->setModel(ForumActiveRecord::findOne(1));

        $response = $this->podium->thread->create(
            $author,
            $forum,
            [
                'name' => 'New Thread',
                'slug' => 'aaa-bbb',
            ]
        );

        self::assertTrue($response->getResult());

        $thread = ThreadActiveRecord::findOne(4);
        self::assertSame(1, $thread->author_id);
        self::assertSame(0, $thread->locked);
        self::assertSame(0, $thread->pinned);
        self::assertSame('New Thread', $thread->name);
        self::assertSame('aaa-bbb', $thread->slug);
        self::assertSame(0, $thread->archived);
        self::assertNull($thread->created_post_at);
        self::assertNull($thread->updated_post_at);
        self::assertEqualsWithDelta(time(), $thread->created_at, 10);
        self::assertEqualsWithDelta(time(), $thread->updated_at, 10);
    }

    public function testEditing(): void
    {
        $repository = new ThreadRepository();
        $repository->setModel(ThreadActiveRecord::findOne(1));
        $response = $this->podium->thread->edit($repository, ['name' => 'Thread Edited']);

        self::assertTrue($response->getResult());

        $thread = ThreadActiveRecord::findOne(1);
        self::assertSame(1, $thread->author_id);
        self::assertSame(0, $thread->locked);
        self::assertSame(0, $thread->pinned);
        self::assertSame('Thread Edited', $thread->name);
        self::assertSame('thread-1', $thread->slug);
        self::assertSame(0, $thread->archived);
        self::assertNull($thread->created_post_at);
        self::assertNull($thread->updated_post_at);
        self::assertNotEqualsWithDelta(time(), $thread->created_at, 10);
        self::assertEqualsWithDelta(time(), $thread->updated_at, 10);
    }

    public function testCreatingWithoutData(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $forum = new ForumRepository();
        $forum->setModel(ForumActiveRecord::findOne(1));

        $response = $this->podium->thread->create($author, $forum);

        self::assertFalse($response->getResult());
        self::assertSame([], $response->getErrors());
    }

    public function testCreatingWithoutName(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $forum = new ForumRepository();
        $forum->setModel(ForumActiveRecord::findOne(1));

        $response = $this->podium->thread->create($author, $forum, ['slug' => 'slug']);

        self::assertFalse($response->getResult());
        self::assertSame(
            ['name' => ['Thread Name cannot be blank.']],
            $response->getErrors()
        );
    }

    public function testCreatingWithTooLongName(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $forum = new ForumRepository();
        $forum->setModel(ForumActiveRecord::findOne(1));

        $response = $this->podium->thread->create($author, $forum, ['name' => str_repeat('a', 192)]);

        self::assertFalse($response->getResult());
        self::assertSame(
            [
                'name' => ['Thread Name should contain at most 191 characters.'],
                'slug' => ['Thread Slug should contain at most 191 characters.']
            ],
            $response->getErrors()
        );
    }

    public function testCreatingWithInvalidSlug(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $forum = new ForumRepository();
        $forum->setModel(ForumActiveRecord::findOne(1));

        $response = $this->podium->thread->create(
            $author,
            $forum,
            [
                'name' => 'name',
                'slug' => '___'
            ]
        );

        self::assertFalse($response->getResult());
        self::assertSame(
            ['slug' => ['Thread Slug is invalid.']],
            $response->getErrors()
        );
    }

    public function testCreatingWithExistingSlug(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $forum = new ForumRepository();
        $forum->setModel(ForumActiveRecord::findOne(1));

        $response = $this->podium->thread->create(
            $author,
            $forum,
            [
                'name' => 'name',
                'slug' => 'thread-2'
            ]
        );

        self::assertFalse($response->getResult());
        self::assertSame(
            ['slug' => ['Thread Slug "thread-2" has already been taken.']],
            $response->getErrors()
        );
    }
}
