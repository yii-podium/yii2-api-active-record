<?php

declare(strict_types=1);

namespace Podium\Tests\Components\Forum;

use Podium\ActiveRecordApi\ActiveRecords\CategoryActiveRecord;
use Podium\ActiveRecordApi\ActiveRecords\ForumActiveRecord;
use Podium\ActiveRecordApi\ActiveRecords\MemberActiveRecord;
use Podium\ActiveRecordApi\Repositories\CategoryRepository;
use Podium\ActiveRecordApi\Repositories\ForumRepository;
use Podium\ActiveRecordApi\Repositories\MemberRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\ForumFixture;

class ForumBuilderTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [ForumFixture::class];
    }

    public function testCreatingWithMinimalData(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $category = new CategoryRepository();
        $category->setModel(CategoryActiveRecord::findOne(1));

        $response = $this->podium->forum->create($author, $category, ['name' => 'New Forum']);

        self::assertTrue($response->getResult());

        $forum = ForumActiveRecord::findOne(4);
        self::assertSame(1, $forum->author_id);
        self::assertSame(1, $forum->author->id);
        self::assertSame(1, $forum->category_id);
        self::assertSame(1, $forum->category->id);
        self::assertSame(1, $forum->visible);
        self::assertSame('New Forum', $forum->name);
        self::assertSame('new-forum', $forum->slug);
        self::assertNull($forum->description);
        self::assertSame(0, $forum->archived);
        self::assertSame(22, $forum->sort);
        self::assertEqualsWithDelta(time(), $forum->created_at, 10);
        self::assertEqualsWithDelta(time(), $forum->updated_at, 10);
    }

    public function testCreatingWithFullData(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $category = new CategoryRepository();
        $category->setModel(CategoryActiveRecord::findOne(1));

        $response = $this->podium->forum->create(
            $author,
            $category,
            [
                'name' => 'New Forum',
                'slug' => 'aaa-bbb',
                'description' => 'Forum About Time',
                'sort' => 15,
            ]
        );

        self::assertTrue($response->getResult());

        $forum = ForumActiveRecord::findOne(4);
        self::assertSame(1, $forum->author_id);
        self::assertSame(1, $forum->visible);
        self::assertSame('New Forum', $forum->name);
        self::assertSame('aaa-bbb', $forum->slug);
        self::assertSame('Forum About Time', $forum->description);
        self::assertSame(0, $forum->archived);
        self::assertSame(15, $forum->sort);
        self::assertEqualsWithDelta(time(), $forum->created_at, 10);
        self::assertEqualsWithDelta(time(), $forum->updated_at, 10);
    }

    public function testEditing(): void
    {
        $repository = new ForumRepository();
        $repository->setModel(ForumActiveRecord::findOne(1));
        $response = $this->podium->forum->edit($repository, ['name' => 'Forum Edited']);

        self::assertTrue($response->getResult());

        $forum = ForumActiveRecord::findOne(1);
        self::assertSame(1, $forum->author_id);
        self::assertSame(1, $forum->visible);
        self::assertSame('Forum Edited', $forum->name);
        self::assertSame('forum-1', $forum->slug);
        self::assertSame('Forum Description', $forum->description);
        self::assertSame(0, $forum->archived);
        self::assertSame(10, $forum->sort);
        self::assertNotEqualsWithDelta(time(), $forum->created_at, 10);
        self::assertEqualsWithDelta(time(), $forum->updated_at, 10);
    }

    public function testCreatingWithoutData(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $category = new CategoryRepository();
        $category->setModel(CategoryActiveRecord::findOne(1));

        $response = $this->podium->forum->create($author, $category);

        self::assertFalse($response->getResult());
        self::assertSame([], $response->getErrors());
    }

    public function testCreatingWithoutName(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $category = new CategoryRepository();
        $category->setModel(CategoryActiveRecord::findOne(1));

        $response = $this->podium->forum->create($author, $category, ['slug' => 'slug']);

        self::assertFalse($response->getResult());
        self::assertSame(
            ['name' => ['Forum Name cannot be blank.']],
            $response->getErrors()
        );
    }

    public function testCreatingWithTooLongName(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $category = new CategoryRepository();
        $category->setModel(CategoryActiveRecord::findOne(1));

        $response = $this->podium->forum->create($author, $category, ['name' => str_repeat('a', 192)]);

        self::assertFalse($response->getResult());
        self::assertSame(
            [
                'name' => ['Forum Name should contain at most 191 characters.'],
                'slug' => ['Forum Slug should contain at most 191 characters.']
            ],
            $response->getErrors()
        );
    }

    public function testCreatingWithTooLongDescription(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $category = new CategoryRepository();
        $category->setModel(CategoryActiveRecord::findOne(1));

        $response = $this->podium->forum->create(
            $author,
            $category,
            [
                'name' => 'name',
                'description' => str_repeat('a', 256),
            ]
        );

        self::assertFalse($response->getResult());
        self::assertSame(
            ['description' => ['Forum Description should contain at most 255 characters.']],
            $response->getErrors()
        );
    }

    public function testCreatingWithStringSort(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $category = new CategoryRepository();
        $category->setModel(CategoryActiveRecord::findOne(1));

        $response = $this->podium->forum->create(
            $author,
            $category,
            [
                'name' => 'name',
                'sort' => 'a',
            ]
        );

        self::assertFalse($response->getResult());
        self::assertSame(
            ['sort' => ['Forum Sort Order must be an integer.']],
            $response->getErrors()
        );
    }

    public function testCreatingWithInvalidSlug(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $category = new CategoryRepository();
        $category->setModel(CategoryActiveRecord::findOne(1));

        $response = $this->podium->forum->create(
            $author,
            $category,
            [
                'name' => 'name',
                'slug' => '___'
            ]
        );

        self::assertFalse($response->getResult());
        self::assertSame(
            ['slug' => ['Forum Slug is invalid.']],
            $response->getErrors()
        );
    }

    public function testCreatingWithExistingSlug(): void
    {
        $author = new MemberRepository();
        $author->setModel(MemberActiveRecord::findOne(1));

        $category = new CategoryRepository();
        $category->setModel(CategoryActiveRecord::findOne(1));

        $response = $this->podium->forum->create(
            $author,
            $category,
            [
                'name' => 'name',
                'slug' => 'forum-2'
            ]
        );

        self::assertFalse($response->getResult());
        self::assertSame(
            ['slug' => ['Forum Slug "forum-2" has already been taken.']],
            $response->getErrors()
        );
    }
}
