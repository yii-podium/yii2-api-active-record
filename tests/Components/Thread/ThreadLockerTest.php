<?php

declare(strict_types=1);

namespace Podium\Tests\Components\Thread;

use Podium\ActiveRecordApi\ActiveRecords\ThreadActiveRecord;
use Podium\ActiveRecordApi\Repositories\ThreadRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\ThreadFixture;

use function time;

class ThreadLockerTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [ThreadFixture::class];
    }

    public function testLocking(): void
    {
        $repository = new ThreadRepository();
        $repository->setModel(ThreadActiveRecord::findOne(1));

        self::assertFalse($repository->isLocked());

        $response = $this->podium->thread->lock($repository);
        self::assertTrue($response->getResult());
        self::assertTrue($repository->isLocked());

        $thread = ThreadActiveRecord::findOne(1);
        self::assertSame(1, $thread->locked);
        self::assertEqualsWithDelta(time(), $thread->updated_at, 10);
    }

    public function testUnlocking(): void
    {
        $repository = new ThreadRepository();
        $repository->setModel(ThreadActiveRecord::findOne(3));

        self::assertTrue($repository->isLocked());

        $response = $this->podium->thread->unlock($repository);
        self::assertTrue($response->getResult());
        self::assertFalse($repository->isLocked());

        $thread = ThreadActiveRecord::findOne(3);
        self::assertSame(0, $thread->locked);
        self::assertEqualsWithDelta(time(), $thread->updated_at, 10);
    }
}
