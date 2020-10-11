<?php

declare(strict_types=1);

namespace Podium\Tests\Category;

use Podium\ActiveRecordApi\ActiveRecords\CategoryActiveRecord;
use Podium\ActiveRecordApi\Repositories\CategoryRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\CategoryFixture;

class CategoryRemoverTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [CategoryFixture::class];
    }

    public function testRemoving(): void
    {
        $category = CategoryActiveRecord::findOne(2);
        $repository = new CategoryRepository();
        $repository->setModel($category);

        $response = $this->podium->category->remove($repository);
        self::assertTrue($response->getResult());

        self::assertNull(CategoryActiveRecord::findOne(2));
    }
}
