<?php

declare(strict_types=1);

namespace Podium\Tests\Components\Thread;

use Podium\ActiveRecordApi\ActiveRecords\ForumActiveRecord;
use Podium\ActiveRecordApi\ActiveRecords\ThreadActiveRecord;
use Podium\ActiveRecordApi\Repositories\ForumRepository;
use Podium\ActiveRecordApi\Repositories\ThreadRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\ThreadFixture;

use function time;

class ThreadMoverTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [ThreadFixture::class];
    }

    public function testMove(): void
    {
        $thread = ThreadActiveRecord::findOne(3);
        $threadRepository = new ThreadRepository();
        $threadRepository->setModel($thread);

        self::assertSame(2, $thread->forum_id);

        $forumNew = ForumActiveRecord::findOne(1);
        $forumRepository = new ForumRepository();
        $forumRepository->setModel($forumNew);

        self::assertSame(1, $forumNew->threads_count);
        self::assertSame(1, $forumNew->posts_count);

        $forumOld = ForumActiveRecord::findOne(2);
        self::assertSame(1, $forumOld->threads_count);
        self::assertSame(1, $forumOld->posts_count);

        $response = $this->podium->thread->move($threadRepository, $forumRepository);
        self::assertTrue($response->getResult());

        $thread = ThreadActiveRecord::findOne(3);
        self::assertSame(1, $thread->forum_id);
        self::assertEqualsWithDelta(time(), $thread->updated_at, 10);

        $forum = ForumActiveRecord::findOne(1);
        self::assertSame(2, $forum->threads_count);
        self::assertSame(2, $forum->posts_count);
    }
}
