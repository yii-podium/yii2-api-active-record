<?php

declare(strict_types=1);

namespace Podium\Tests\Member;

use Podium\ActiveRecordApi\ActiveRecords\AcquaintanceActiveRecord;
use Podium\ActiveRecordApi\ActiveRecords\MemberActiveRecord;
use Podium\ActiveRecordApi\Enums\AcquaintanceType;
use Podium\ActiveRecordApi\Repositories\MemberRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\AcquaintanceFixture;

class MemberAcquaintanceTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [AcquaintanceFixture::class];
    }

    public function testBefriending(): void
    {
        $member = new MemberRepository();
        $member->setModel(MemberActiveRecord::findOne(1));
        $target = new MemberRepository();
        $target->setModel(MemberActiveRecord::findOne(2));

        $response = $this->podium->member->befriend($member, $target);

        self::assertTrue($response->getResult());

        $acquaintance = AcquaintanceActiveRecord::findOne(
            [
                'member_id' => 1,
                'target_id' => 2,
            ]
        );
        self::assertSame(AcquaintanceType::FRIEND, $acquaintance->type_id);
        self::assertEqualsWithDelta(time(), $acquaintance->created_at, 10);
        self::assertEqualsWithDelta(time(), $acquaintance->updated_at, 10);
    }
}
