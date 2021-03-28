<?php

declare(strict_types=1);

namespace Podium\Tests\Components\Forum;

use Podium\ActiveRecordApi\ActiveRecords\ForumActiveRecord;
use Podium\ActiveRecordApi\Repositories\ForumRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\ForumFixture;

class ForumRemoverTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [ForumFixture::class];
    }

    public function testRemoving(): void
    {
        $repository = new ForumRepository();
        $repository->setModel(ForumActiveRecord::findOne(2));

        $response = $this->podium->forum->remove($repository);
        self::assertTrue($response->getResult());

        self::assertNull(ForumActiveRecord::findOne(2));
    }
}
