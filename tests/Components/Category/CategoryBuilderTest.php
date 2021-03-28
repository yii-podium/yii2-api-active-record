<?php

declare(strict_types=1);

namespace Podium\Tests\Components\Category;

use Podium\ActiveRecordApi\ActiveRecords\CategoryActiveRecord;
use Podium\ActiveRecordApi\ActiveRecords\MemberActiveRecord;
use Podium\ActiveRecordApi\Repositories\CategoryRepository;
use Podium\ActiveRecordApi\Repositories\MemberRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\CategoryFixture;

use function str_repeat;

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
        self::assertSame(1, $category->author->id);
        self::assertSame(1, $category->visible);
        self::assertSame('New Category', $category->name);
        self::assertSame('new-category', $category->slug);
        self::assertNull($category->description);
        self::assertSame(0, $category->archived);
        self::assertSame(22, $category->sort);
        self::assertEqualsWithDelta(time(), $category->created_at, 10);
        self::assertEqualsWithDelta(time(), $category->updated_at, 10);
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
        self::assertEqualsWithDelta(time(), $category->created_at, 10);
        self::assertEqualsWithDelta(time(), $category->updated_at, 10);
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
        self::assertNotEqualsWithDelta(time(), $category->created_at, 10);
        self::assertEqualsWithDelta(time(), $category->updated_at, 10);
    }

    public function testCreatingWithoutData(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $response = $this->podium->category->create($author);

        self::assertFalse($response->getResult());
        self::assertSame([], $response->getErrors());
    }

    public function testCreatingWithoutName(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $response = $this->podium->category->create($author, ['slug' => 'slug']);

        self::assertFalse($response->getResult());
        self::assertSame(
            ['name' => ['Category Name cannot be blank.']],
            $response->getErrors()
        );
    }

    public function testCreatingWithTooLongName(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $response = $this->podium->category->create($author, ['name' => str_repeat('a', 192)]);

        self::assertFalse($response->getResult());
        self::assertSame(
            [
                'name' => ['Category Name should contain at most 191 characters.'],
                'slug' => ['Category Slug should contain at most 191 characters.']
            ],
            $response->getErrors()
        );
    }

    public function testCreatingWithTooLongDescription(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $response = $this->podium->category->create(
            $author,
            [
                'name' => 'name',
                'description' => str_repeat('a', 256),
            ]
        );

        self::assertFalse($response->getResult());
        self::assertSame(
            ['description' => ['Category Description should contain at most 255 characters.']],
            $response->getErrors()
        );
    }

    public function testCreatingWithStringSort(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $response = $this->podium->category->create(
            $author,
            [
                'name' => 'name',
                'sort' => 'a',
            ]
        );

        self::assertFalse($response->getResult());
        self::assertSame(
            ['sort' => ['Category Sort Order must be an integer.']],
            $response->getErrors()
        );
    }

    public function testCreatingWithInvalidSlug(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $response = $this->podium->category->create(
            $author,
            [
                'name' => 'name',
                'slug' => '___'
            ]
        );

        self::assertFalse($response->getResult());
        self::assertSame(
            ['slug' => ['Category Slug is invalid.']],
            $response->getErrors()
        );
    }

    public function testCreatingWithExistingSlug(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $response = $this->podium->category->create(
            $author,
            [
                'name' => 'name',
                'slug' => 'category-2'
            ]
        );

        self::assertFalse($response->getResult());
        self::assertSame(
            ['slug' => ['Category Slug "category-2" has already been taken.']],
            $response->getErrors()
        );
    }
}
