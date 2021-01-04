<?php

declare(strict_types=1);

namespace Podium\Tests\Forum;

use Podium\ActiveRecordApi\ActiveRecords\CategoryActiveRecord;
use Podium\ActiveRecordApi\ActiveRecords\ForumActiveRecord;
use Podium\ActiveRecordApi\Repositories\CategoryRepository;
use Podium\ActiveRecordApi\Repositories\ForumRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\ForumFixture;

use function time;

class ForumMoverTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [ForumFixture::class];
    }

    public function testMove(): void
    {
        $forum = ForumActiveRecord::findOne(3);
        $forumRepository = new ForumRepository();
        $forumRepository->setModel($forum);

        self::assertSame(2, $forum->category_id);

        $categoryRepository = new CategoryRepository();
        $categoryRepository->setModel(CategoryActiveRecord::findOne(1));

        $response = $this->podium->forum->move($forumRepository, $categoryRepository);
        self::assertTrue($response->getResult());

        $forum = ForumActiveRecord::findOne(3);
        self::assertSame(1, $forum->category_id);
        self::assertEqualsWithDelta(time(), $forum->updated_at, 10);
    }
}
