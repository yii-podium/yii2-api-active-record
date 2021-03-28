<?php

declare(strict_types=1);

namespace Podium\Tests\Components\Forum;

use Podium\ActiveRecordApi\ActiveRecords\ForumActiveRecord;
use Podium\ActiveRecordApi\ActiveRecords\ForumGroupActiveRecord;
use Podium\ActiveRecordApi\ActiveRecords\GroupActiveRecord;
use Podium\ActiveRecordApi\Repositories\ForumRepository;
use Podium\ActiveRecordApi\Repositories\GroupRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\ForumGroupFixture;

use function time;

class ForumGroupTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [ForumGroupFixture::class];
    }

    public function testJoining(): void
    {
        $repository = new ForumRepository();
        $repository->setModel(ForumActiveRecord::findOne(2));

        self::assertEmpty($repository->getGroups());

        $group = new GroupRepository();
        $group->setModel(GroupActiveRecord::findOne(1));

        $response = $this->podium->group->join($group, $repository);
        self::assertTrue($response->getResult());

        self::assertNotEmpty($repository->getGroups());
        self::assertTrue($repository->hasGroups([$group]));

        $forumGroup = ForumGroupActiveRecord::findOne(2);
        self::assertSame(2, $forumGroup->forum_id);
        self::assertSame(2, $forumGroup->forum->id);
        self::assertSame(1, $forumGroup->group_id);
        self::assertSame(1, $forumGroup->group->id);
        self::assertEqualsWithDelta(time(), $forumGroup->created_at, 10);
    }

    public function testLeaving(): void
    {
        $repository = new ForumRepository();
        $repository->setModel(ForumActiveRecord::findOne(1));

        self::assertNotEmpty($repository->getGroups());

        $group = new GroupRepository();
        $group->setModel(GroupActiveRecord::findOne(1));

        $response = $this->podium->group->leave($group, $repository);
        self::assertTrue($response->getResult());

        self::assertEmpty($repository->getGroups());

        self::assertNull(ForumGroupActiveRecord::findOne(1));
    }
}
