<?php

declare(strict_types=1);

namespace Podium\Tests\Member;

use Podium\ActiveRecordApi\ActiveRecords\MemberActiveRecord;
use Podium\ActiveRecordApi\Repositories\MemberRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\MemberFixture;

class MemberRemoverTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [MemberFixture::class];
    }

    public function testRemoving(): void
    {
        $member = MemberActiveRecord::findOne(1);
        $repository = new MemberRepository();
        $repository->setModel($member);

        $response = $this->podium->member->remove($repository);
        self::assertTrue($response->getResult());

        self::assertNull(MemberActiveRecord::findOne(1));
    }
}
