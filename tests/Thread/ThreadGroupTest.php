<?php

declare(strict_types=1);

namespace Podium\Tests\Thread;

use Podium\ActiveRecordApi\ActiveRecords\GroupActiveRecord;
use Podium\ActiveRecordApi\ActiveRecords\ThreadActiveRecord;
use Podium\ActiveRecordApi\ActiveRecords\ThreadGroupActiveRecord;
use Podium\ActiveRecordApi\Repositories\GroupRepository;
use Podium\ActiveRecordApi\Repositories\ThreadRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\ThreadGroupFixture;

use function time;

class ThreadGroupTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [ThreadGroupFixture::class];
    }

    public function testJoining(): void
    {
        $repository = new ThreadRepository();
        $repository->setModel(ThreadActiveRecord::findOne(2));

        self::assertEmpty($repository->getGroups());

        $group = new GroupRepository();
        $group->setModel(GroupActiveRecord::findOne(1));

        $response = $this->podium->group->join($group, $repository);
        self::assertTrue($response->getResult());

        self::assertNotEmpty($repository->getGroups());
        self::assertTrue($repository->hasGroups([$group]));

        $threadGroup = ThreadGroupActiveRecord::findOne(2);
        self::assertSame(2, $threadGroup->thread_id);
        self::assertSame(2, $threadGroup->thread->id);
        self::assertSame(1, $threadGroup->group_id);
        self::assertSame(1, $threadGroup->group->id);
        self::assertEqualsWithDelta(time(), $threadGroup->created_at, 10);
    }

    public function testLeaving(): void
    {
        $repository = new ThreadRepository();
        $repository->setModel(ThreadActiveRecord::findOne(1));

        self::assertNotEmpty($repository->getGroups());

        $group = new GroupRepository();
        $group->setModel(GroupActiveRecord::findOne(1));

        $response = $this->podium->group->leave($group, $repository);
        self::assertTrue($response->getResult());

        self::assertEmpty($repository->getGroups());

        self::assertNull(ThreadGroupActiveRecord::findOne(1));
    }
}
