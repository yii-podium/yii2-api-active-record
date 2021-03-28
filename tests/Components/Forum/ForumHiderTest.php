<?php

declare(strict_types=1);

namespace Podium\Tests\Components\Forum;

use Podium\ActiveRecordApi\ActiveRecords\ForumActiveRecord;
use Podium\ActiveRecordApi\Repositories\ForumRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\ForumFixture;

use function time;

class ForumHiderTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [ForumFixture::class];
    }

    public function testHiding(): void
    {
        $repository = new ForumRepository();
        $repository->setModel(ForumActiveRecord::findOne(1));

        self::assertFalse($repository->isHidden());

        $response = $this->podium->forum->hide($repository);
        self::assertTrue($response->getResult());
        self::assertTrue($repository->isHidden());

        $forum = ForumActiveRecord::findOne(1);
        self::assertSame(0, $forum->visible);
        self::assertEqualsWithDelta(time(), $forum->updated_at, 10);
    }

    public function testRevealing(): void
    {
        $repository = new ForumRepository();
        $repository->setModel(ForumActiveRecord::findOne(3));

        self::assertTrue($repository->isHidden());

        $response = $this->podium->forum->reveal($repository);
        self::assertTrue($response->getResult());
        self::assertFalse($repository->isHidden());

        $forum = ForumActiveRecord::findOne(3);
        self::assertSame(1, $forum->visible);
        self::assertEqualsWithDelta(time(), $forum->updated_at, 10);
    }
}
