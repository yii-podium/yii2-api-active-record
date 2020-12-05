<?php

declare(strict_types=1);

namespace Podium\Tests\Group;

use Podium\ActiveRecordApi\ActiveRecords\GroupActiveRecord;
use Podium\ActiveRecordApi\Repositories\GroupRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\GroupFixture;

class GroupBuilderTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [GroupFixture::class];
    }

    public function testCreating(): void
    {
        $response = $this->podium->group->create(['name' => 'New Group']);

        self::assertTrue($response->getResult());

        $group = GroupActiveRecord::findOne(2);
        self::assertSame('New Group', $group->name);
        self::assertEqualsWithDelta(time(), $group->created_at, 10);
        self::assertEqualsWithDelta(time(), $group->updated_at, 10);
    }

    public function testEditing(): void
    {
        $repository = new GroupRepository();
        $repository->setModel(GroupActiveRecord::findOne(1));
        $response = $this->podium->group->edit($repository, ['name' => 'Group Edited']);

        self::assertTrue($response->getResult());

        $group = GroupActiveRecord::findOne(1);
        self::assertSame('Group Edited', $group->name);
        self::assertNotEqualsWithDelta(time(), $group->created_at, 10);
        self::assertEqualsWithDelta(time(), $group->updated_at, 10);
    }
}
