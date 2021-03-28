<?php

declare(strict_types=1);

namespace Podium\Tests\Components\Category;

use Podium\ActiveRecordApi\ActiveRecords\CategoryActiveRecord;
use Podium\ActiveRecordApi\ActiveRecords\CategoryGroupActiveRecord;
use Podium\ActiveRecordApi\ActiveRecords\GroupActiveRecord;
use Podium\ActiveRecordApi\Repositories\CategoryRepository;
use Podium\ActiveRecordApi\Repositories\GroupRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\CategoryGroupFixture;

use function time;

class CategoryGroupTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [CategoryGroupFixture::class];
    }

    public function testJoining(): void
    {
        $repository = new CategoryRepository();
        $repository->setModel(CategoryActiveRecord::findOne(2));

        self::assertEmpty($repository->getGroups());

        $group = new GroupRepository();
        $group->setModel(GroupActiveRecord::findOne(1));

        $response = $this->podium->group->join($group, $repository);
        self::assertTrue($response->getResult());

        self::assertNotEmpty($repository->getGroups());
        self::assertTrue($repository->hasGroups([$group]));

        $categoryGroup = CategoryGroupActiveRecord::findOne(2);
        self::assertSame(2, $categoryGroup->category_id);
        self::assertSame(2, $categoryGroup->category->id);
        self::assertSame(1, $categoryGroup->group_id);
        self::assertSame(1, $categoryGroup->group->id);
        self::assertEqualsWithDelta(time(), $categoryGroup->created_at, 10);
    }

    public function testLeaving(): void
    {
        $repository = new CategoryRepository();
        $repository->setModel(CategoryActiveRecord::findOne(1));

        self::assertNotEmpty($repository->getGroups());

        $group = new GroupRepository();
        $group->setModel(GroupActiveRecord::findOne(1));

        $response = $this->podium->group->leave($group, $repository);
        self::assertTrue($response->getResult());

        self::assertEmpty($repository->getGroups());

        self::assertNull(CategoryGroupActiveRecord::findOne(1));
    }
}
