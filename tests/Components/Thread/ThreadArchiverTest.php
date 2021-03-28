<?php

declare(strict_types=1);

namespace Podium\Tests\Components\Thread;

use Podium\ActiveRecordApi\ActiveRecords\ThreadActiveRecord;
use Podium\ActiveRecordApi\Repositories\ThreadRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\ThreadFixture;

use function time;

class ThreadArchiverTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [ThreadFixture::class];
    }

    public function testArchiving(): void
    {
        $repository = new ThreadRepository();
        $repository->setModel(ThreadActiveRecord::findOne(1));

        self::assertFalse($repository->isArchived());

        $response = $this->podium->thread->archive($repository);
        self::assertTrue($response->getResult());
        self::assertTrue($repository->isArchived());

        $thread = ThreadActiveRecord::findOne(1);
        self::assertSame(1, $thread->archived);
        self::assertEqualsWithDelta(time(), $thread->updated_at, 10);
    }

    public function testReviving(): void
    {
        $repository = new ThreadRepository();
        $repository->setModel(ThreadActiveRecord::findOne(2));

        self::assertTrue($repository->isArchived());

        $response = $this->podium->thread->revive($repository);
        self::assertTrue($response->getResult());
        self::assertFalse($repository->isArchived());

        $thread = ThreadActiveRecord::findOne(2);
        self::assertSame(0, $thread->archived);
        self::assertEqualsWithDelta(time(), $thread->updated_at, 10);
    }
}
