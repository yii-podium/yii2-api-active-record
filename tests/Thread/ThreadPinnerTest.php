<?php

declare(strict_types=1);

namespace Podium\Tests\Thread;

use Podium\ActiveRecordApi\ActiveRecords\ThreadActiveRecord;
use Podium\ActiveRecordApi\Repositories\ThreadRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\ThreadFixture;

use function time;

class ThreadPinnerTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [ThreadFixture::class];
    }

    public function testPinning(): void
    {
        $thread = ThreadActiveRecord::findOne(1);
        $repository = new ThreadRepository();
        $repository->setModel($thread);

        self::assertSame(0, $thread->pinned);

        $response = $this->podium->thread->pin($repository);
        self::assertTrue($response->getResult());

        $thread = ThreadActiveRecord::findOne(1);
        self::assertSame(1, $thread->pinned);
        self::assertEqualsWithDelta(time(), $thread->updated_at, 10);
    }

    public function testUnpinning(): void
    {
        $thread = ThreadActiveRecord::findOne(3);
        $repository = new ThreadRepository();
        $repository->setModel($thread);

        self::assertSame(1, $thread->pinned);

        $response = $this->podium->thread->unpin($repository);
        self::assertTrue($response->getResult());

        $thread = ThreadActiveRecord::findOne(3);
        self::assertSame(0, $thread->pinned);
        self::assertEqualsWithDelta(time(), $thread->updated_at, 10);
    }
}
