<?php

declare(strict_types=1);

namespace Podium\Tests\Log;

use Podium\ActiveRecordApi\ActiveRecords\LogActiveRecord;
use Podium\ActiveRecordApi\ActiveRecords\MemberActiveRecord;
use Podium\ActiveRecordApi\Repositories\MemberRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\LogFixture;

class LogBuilderTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [LogFixture::class];
    }

    public function testCreating(): void
    {
        $member = new MemberRepository();
        $member->setModel(MemberActiveRecord::findOne(1));

        $response = $this->podium->log->create($member, 'Test Action');

        self::assertTrue($response->getResult());

        $log = LogActiveRecord::findOne(2);
        self::assertSame('Test Action', $log->action);
        self::assertSame(1, $log->member_id);
        self::assertSame(1, $log->member->id);
        self::assertEqualsWithDelta(time(), $log->created_at, 10);
        self::assertEqualsWithDelta(time(), $log->updated_at, 10);
    }
}
