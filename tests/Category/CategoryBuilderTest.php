<?php

declare(strict_types=1);

namespace Podium\Tests\Category;

use Podium\ActiveRecordApi\ActiveRecords\CategoryActiveRecord;
use Podium\ActiveRecordApi\ActiveRecords\MemberActiveRecord;
use Podium\ActiveRecordApi\Repositories\CategoryRepository;
use Podium\ActiveRecordApi\Repositories\MemberRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\CategoryFixture;

class CategoryBuilderTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [CategoryFixture::class];
    }

    public function testCreatingWithMinimalData(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $response = $this->podium->category->create($author, ['name' => 'New Category']);

        self::assertTrue($response->getResult());

        $category = CategoryActiveRecord::findOne(4);
        self::assertSame(1, $category->author_id);
        self::assertSame(1, $category->visible);
        self::assertSame('New Category', $category->name);
        self::assertSame('new-category', $category->slug);
        self::assertNull($category->description);
        self::assertSame(0, $category->archived);
        self::assertSame(22, $category->sort);
    }

    public function testCreatingWithFullData(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $response = $this->podium->category->create(
            $author,
            [
                'name' => 'New Category',
                'slug' => 'aaa-bbb',
                'description' => 'Category About Time',
                'sort' => 15,
            ]
        );

        self::assertTrue($response->getResult());

        $category = CategoryActiveRecord::findOne(4);
        self::assertSame(1, $category->author_id);
        self::assertSame(1, $category->visible);
        self::assertSame('New Category', $category->name);
        self::assertSame('aaa-bbb', $category->slug);
        self::assertSame('Category About Time', $category->description);
        self::assertSame(0, $category->archived);
        self::assertSame(15, $category->sort);
    }

    public function testEditing(): void
    {
        $repository = new CategoryRepository();
        $repository->setModel(CategoryActiveRecord::findOne(1));
        $response = $this->podium->category->edit($repository, ['name' => 'Category Edited']);

        self::assertTrue($response->getResult());

        $category = CategoryActiveRecord::findOne(1);
        self::assertSame(1, $category->author_id);
        self::assertSame(1, $category->visible);
        self::assertSame('Category Edited', $category->name);
        self::assertSame('category-1', $category->slug);
        self::assertSame('Category Description', $category->description);
        self::assertSame(0, $category->archived);
        self::assertSame(10, $category->sort);
    }
}
