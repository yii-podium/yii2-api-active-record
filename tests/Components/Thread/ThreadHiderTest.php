<?php

declare(strict_types=1);

namespace Podium\Tests\Components\Thread;

use Podium\ActiveRecordApi\ActiveRecords\ThreadActiveRecord;
use Podium\ActiveRecordApi\Repositories\ThreadRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\ThreadFixture;

use function time;

class ThreadHiderTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [ThreadFixture::class];
    }

    public function testHiding(): void
    {
        $repository = new ThreadRepository();
        $repository->setModel(ThreadActiveRecord::findOne(1));

        self::assertFalse($repository->isHidden());

        $response = $this->podium->thread->hide($repository);
        self::assertTrue($response->getResult());
        self::assertTrue($repository->isHidden());

        $thread = ThreadActiveRecord::findOne(1);
        self::assertSame(0, $thread->visible);
        self::assertEqualsWithDelta(time(), $thread->updated_at, 10);
    }

    public function testRevealing(): void
    {
        $repository = new ThreadRepository();
        $repository->setModel(ThreadActiveRecord::findOne(3));

        self::assertTrue($repository->isHidden());

        $response = $this->podium->thread->reveal($repository);
        self::assertTrue($response->getResult());
        self::assertFalse($repository->isHidden());

        $thread = ThreadActiveRecord::findOne(3);
        self::assertSame(1, $thread->visible);
        self::assertEqualsWithDelta(time(), $thread->updated_at, 10);
    }
}
