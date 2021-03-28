<?php

declare(strict_types=1);

namespace Podium\Tests\Components\Category;

use Podium\ActiveRecordApi\ActiveRecords\CategoryActiveRecord;
use Podium\ActiveRecordApi\Repositories\CategoryRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\CategoryFixture;

class CategorySorterTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [CategoryFixture::class];
    }

    public function testReplacing(): void
    {
        $category1 = CategoryActiveRecord::findOne(1);
        self::assertSame(10, $category1->sort);

        $category2 = CategoryActiveRecord::findOne(2);
        self::assertSame(21, $category2->sort);

        $repository1 = new CategoryRepository();
        $repository1->setModel($category1);
        $repository2 = new CategoryRepository();
        $repository2->setModel($category2);

        $response = $this->podium->category->replace($repository1, $repository2);
        self::assertTrue($response->getResult());

        $category1 = CategoryActiveRecord::findOne(1);
        self::assertSame(21, $category1->sort);
        self::assertEqualsWithDelta(time(), $category1->updated_at, 10);

        $category2 = CategoryActiveRecord::findOne(2);
        self::assertSame(10, $category2->sort);
        self::assertEqualsWithDelta(time(), $category2->updated_at, 10);
    }

    public function testSorting(): void
    {
        $response = $this->podium->category->sort();
        self::assertTrue($response->getResult());

        $category1 = CategoryActiveRecord::findOne(3);
        self::assertSame(0, $category1->sort);
        self::assertEqualsWithDelta(time(), $category1->updated_at, 10);
        $category2 = CategoryActiveRecord::findOne(1);
        self::assertSame(1, $category2->sort);
        self::assertEqualsWithDelta(time(), $category2->updated_at, 10);
        $category3 = CategoryActiveRecord::findOne(2);
        self::assertSame(2, $category3->sort);
        self::assertEqualsWithDelta(time(), $category3->updated_at, 10);
    }
}
