<?php

declare(strict_types=1);

namespace Podium\Tests\Components\Group;

use Podium\ActiveRecordApi\ActiveRecords\GroupActiveRecord;
use Podium\ActiveRecordApi\Repositories\GroupRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\GroupFixture;

class GroupRemoverTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [GroupFixture::class];
    }

    public function testRemoving(): void
    {
        $repository = new GroupRepository();
        $repository->setModel(GroupActiveRecord::findOne(1));

        $response = $this->podium->group->remove($repository);
        self::assertTrue($response->getResult());

        self::assertNull(GroupActiveRecord::findOne(1));
    }
}
