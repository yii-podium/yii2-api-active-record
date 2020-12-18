<?php

declare(strict_types=1);

namespace Podium\Tests\Thread;

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
}
