<?php

declare(strict_types=1);

namespace Podium\Tests\Log;

use Podium\ActiveRecordApi\ActiveRecords\LogActiveRecord;
use Podium\ActiveRecordApi\Repositories\LogRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\LogFixture;

class LogRemoverTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [LogFixture::class];
    }

    public function testRemoving(): void
    {
        $repository = new LogRepository();
        $repository->setModel(LogActiveRecord::findOne(1));

        $response = $this->podium->log->remove($repository);
        self::assertTrue($response->getResult());

        self::assertNull(LogActiveRecord::findOne(1));
    }
}
