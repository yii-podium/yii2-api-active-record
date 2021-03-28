<?php

declare(strict_types=1);

namespace Podium\Tests\Components\Forum;

use Podium\ActiveRecordApi\ActiveRecords\ForumActiveRecord;
use Podium\ActiveRecordApi\Repositories\ForumRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\ForumFixture;

use function time;

class ForumArchiverTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [ForumFixture::class];
    }

    public function testArchiving(): void
    {
        $repository = new ForumRepository();
        $repository->setModel(ForumActiveRecord::findOne(1));

        self::assertFalse($repository->isArchived());

        $response = $this->podium->forum->archive($repository);
        self::assertTrue($response->getResult());
        self::assertTrue($repository->isArchived());

        $forum = ForumActiveRecord::findOne(1);
        self::assertSame(1, $forum->archived);
        self::assertEqualsWithDelta(time(), $forum->updated_at, 10);
    }

    public function testReviving(): void
    {
        $repository = new ForumRepository();
        $repository->setModel(ForumActiveRecord::findOne(2));

        self::assertTrue($repository->isArchived());

        $response = $this->podium->forum->revive($repository);
        self::assertTrue($response->getResult());
        self::assertFalse($repository->isArchived());

        $forum = ForumActiveRecord::findOne(2);
        self::assertSame(0, $forum->archived);
        self::assertEqualsWithDelta(time(), $forum->updated_at, 10);
    }
}
