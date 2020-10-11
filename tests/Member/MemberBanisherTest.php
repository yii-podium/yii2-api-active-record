<?php

declare(strict_types=1);

namespace Podium\Tests\Member;

use Podium\ActiveRecordApi\ActiveRecords\MemberActiveRecord;
use Podium\ActiveRecordApi\Enums\MemberStatus;
use Podium\ActiveRecordApi\Repositories\MemberRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\MemberFixture;

use function time;

class MemberBanisherTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [MemberFixture::class];
    }

    public function testBanning(): void
    {
        $member = MemberActiveRecord::findOne(1);
        self::assertSame(MemberStatus::ACTIVE, $member->status_id);

        $repository = new MemberRepository();
        $repository->setModel($member);

        $response = $this->podium->member->ban($repository);
        self::assertTrue($response->getResult());
        self::assertTrue($repository->isBanned());

        $member = MemberActiveRecord::findOne(1);
        self::assertSame(MemberStatus::BANNED, $member->status_id);
        self::assertEqualsWithDelta(time(), $member->updated_at, 10);
    }

    public function testUnbanning(): void
    {
        $member = MemberActiveRecord::findOne(2);
        self::assertSame(MemberStatus::BANNED, $member->status_id);

        $repository = new MemberRepository();
        $repository->setModel($member);

        $response = $this->podium->member->unban($repository);
        self::assertTrue($response->getResult());
        self::assertFalse($repository->isBanned());

        $member = MemberActiveRecord::findOne(2);
        self::assertSame(MemberStatus::ACTIVE, $member->status_id);
        self::assertEqualsWithDelta(time(), $member->updated_at, 10);
    }

    public function testFailedBanning(): void
    {
        $member = MemberActiveRecord::findOne(2);
        self::assertSame(MemberStatus::BANNED, $member->status_id);

        $repository = new MemberRepository();
        $repository->setModel($member);

        $response = $this->podium->member->ban($repository);
        self::assertFalse($response->getResult());
        self::assertSame(['api' => 'Member is already banned.'], $response->getErrors());
    }

    public function testFailedUnbanning(): void
    {
        $member = MemberActiveRecord::findOne(1);
        self::assertSame(MemberStatus::ACTIVE, $member->status_id);

        $repository = new MemberRepository();
        $repository->setModel($member);

        $response = $this->podium->member->unban($repository);
        self::assertFalse($response->getResult());
        self::assertSame(['api' => 'Member has not been banned.'], $response->getErrors());
    }
}
