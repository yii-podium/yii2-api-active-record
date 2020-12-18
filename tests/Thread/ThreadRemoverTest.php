<?php

declare(strict_types=1);

namespace Podium\Tests\Thread;

use Podium\ActiveRecordApi\ActiveRecords\ThreadActiveRecord;
use Podium\ActiveRecordApi\Repositories\ThreadRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\ThreadFixture;

class ThreadRemoverTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [ThreadFixture::class];
    }

    public function testRemoving(): void
    {
        $repository = new ThreadRepository();
        $repository->setModel(ThreadActiveRecord::findOne(2));

        $response = $this->podium->thread->remove($repository);
        self::assertTrue($response->getResult());

        self::assertNull(ThreadActiveRecord::findOne(2));
    }
}
