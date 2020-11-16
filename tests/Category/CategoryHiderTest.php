<?php

declare(strict_types=1);

namespace Podium\Tests\Category;

use Podium\ActiveRecordApi\ActiveRecords\CategoryActiveRecord;
use Podium\ActiveRecordApi\Repositories\CategoryRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\CategoryFixture;

use function time;

class CategoryHiderTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [CategoryFixture::class];
    }

    public function testHiding(): void
    {
        $repository = new CategoryRepository();
        $repository->setModel(CategoryActiveRecord::findOne(1));

        self::assertFalse($repository->isHidden());

        $response = $this->podium->category->hide($repository);
        self::assertTrue($response->getResult());
        self::assertTrue($repository->isHidden());

        $category = CategoryActiveRecord::findOne(1);
        self::assertSame(0, $category->visible);
        self::assertEqualsWithDelta(time(), $category->updated_at, 10);
    }

    public function testRevealing(): void
    {
        $repository = new CategoryRepository();
        $repository->setModel(CategoryActiveRecord::findOne(3));

        self::assertTrue($repository->isHidden());

        $response = $this->podium->category->reveal($repository);
        self::assertTrue($response->getResult());
        self::assertFalse($repository->isHidden());

        $category = CategoryActiveRecord::findOne(3);
        self::assertSame(1, $category->visible);
        self::assertEqualsWithDelta(time(), $category->updated_at, 10);
    }
}
