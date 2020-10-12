<?php

declare(strict_types=1);

namespace Podium\Tests\Category;

use Podium\ActiveRecordApi\ActiveRecords\CategoryActiveRecord;
use Podium\ActiveRecordApi\Repositories\CategoryRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\CategoryFixture;

use function time;

class CategoryArchiverTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [CategoryFixture::class];
    }

    public function testArchiving(): void
    {
        $category = CategoryActiveRecord::findOne(1);
        $repository = new CategoryRepository();
        $repository->setModel($category);

        self::assertFalse($repository->isArchived());

        $response = $this->podium->category->archive($repository);
        self::assertTrue($response->getResult());
        self::assertTrue($repository->isArchived());

        $category = CategoryActiveRecord::findOne(1);
        self::assertSame(1, $category->archived);
        self::assertEqualsWithDelta(time(), $category->updated_at, 10);
    }

    public function testReviving(): void
    {
        $category = CategoryActiveRecord::findOne(2);
        $repository = new CategoryRepository();
        $repository->setModel($category);

        self::assertTrue($repository->isArchived());

        $response = $this->podium->category->revive($repository);
        self::assertTrue($response->getResult());
        self::assertFalse($repository->isArchived());

        $category = CategoryActiveRecord::findOne(2);
        self::assertSame(0, $category->archived);
        self::assertEqualsWithDelta(time(), $category->updated_at, 10);
    }
}
