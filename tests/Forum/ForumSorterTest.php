<?php

declare(strict_types=1);

namespace Podium\Tests\Forum;

use Podium\ActiveRecordApi\ActiveRecords\ForumActiveRecord;
use Podium\ActiveRecordApi\Repositories\ForumRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\ForumFixture;

class ForumSorterTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [ForumFixture::class];
    }

    public function testReplacing(): void
    {
        $forum1 = ForumActiveRecord::findOne(1);
        self::assertSame(10, $forum1->sort);

        $forum2 = ForumActiveRecord::findOne(2);
        self::assertSame(21, $forum2->sort);

        $repository1 = new ForumRepository();
        $repository1->setModel($forum1);
        $repository2 = new ForumRepository();
        $repository2->setModel($forum2);

        $response = $this->podium->forum->replace($repository1, $repository2);
        self::assertTrue($response->getResult());

        $forum1 = ForumActiveRecord::findOne(1);
        self::assertSame(21, $forum1->sort);
        self::assertEqualsWithDelta(time(), $forum1->updated_at, 10);

        $forum2 = ForumActiveRecord::findOne(2);
        self::assertSame(10, $forum2->sort);
        self::assertEqualsWithDelta(time(), $forum2->updated_at, 10);
    }

    public function testSorting(): void
    {
        $response = $this->podium->forum->sort();
        self::assertTrue($response->getResult());

        $forum1 = ForumActiveRecord::findOne(3);
        self::assertSame(0, $forum1->sort);
        self::assertEqualsWithDelta(time(), $forum1->updated_at, 10);
        $forum2 = ForumActiveRecord::findOne(1);
        self::assertSame(1, $forum2->sort);
        self::assertEqualsWithDelta(time(), $forum2->updated_at, 10);
        $forum3 = ForumActiveRecord::findOne(2);
        self::assertSame(2, $forum3->sort);
        self::assertEqualsWithDelta(time(), $forum3->updated_at, 10);
    }
}
