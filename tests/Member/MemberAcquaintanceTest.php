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

    public function testUnfriending(): void
    {
        $member = new MemberRepository();
        $member->setModel(MemberActiveRecord::findOne(2));
        $target = new MemberRepository();
        $target->setModel(MemberActiveRecord::findOne(1));

        $response = $this->podium->member->unfriend($member, $target);

        self::assertTrue($response->getResult());

        self::assertNull(
            AcquaintanceActiveRecord::findOne(
                [
                    'member_id' => 2,
                    'target_id' => 1,
                ]
            )
        );
    }

    public function testIgnoring(): void
    {
        $member = new MemberRepository();
        $member->setModel(MemberActiveRecord::findOne(1));
        $target = new MemberRepository();
        $target->setModel(MemberActiveRecord::findOne(3));

        $response = $this->podium->member->ignore($member, $target);

        self::assertTrue($response->getResult());

        $acquaintance = AcquaintanceActiveRecord::findOne(
            [
                'member_id' => 1,
                'target_id' => 3,
            ]
        );
        self::assertSame(AcquaintanceType::IGNORE, $acquaintance->type_id);
        self::assertEqualsWithDelta(time(), $acquaintance->created_at, 10);
        self::assertEqualsWithDelta(time(), $acquaintance->updated_at, 10);
    }

    public function testUnignoring(): void
    {
        $member = new MemberRepository();
        $member->setModel(MemberActiveRecord::findOne(2));
        $target = new MemberRepository();
        $target->setModel(MemberActiveRecord::findOne(3));

        $response = $this->podium->member->unignore($member, $target);

        self::assertTrue($response->getResult());

        self::assertNull(
            AcquaintanceActiveRecord::findOne(
                [
                    'member_id' => 2,
                    'target_id' => 3,
                ]
            )
        );
    }
}
