<?php

declare(strict_types=1);

namespace Podium\Tests\Repositories;

use DomainException;
use LogicException;
use PHPUnit\Framework\TestCase;
use Podium\ActiveRecordApi\Repositories\CategoryRepository;
use Podium\ActiveRecordApi\Repositories\GroupRepository;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Tests\Stubs\ActiveDataFilterStub;
use Podium\Tests\Stubs\BookmarkActiveRecordStub;
use Podium\Tests\Stubs\CategoryActiveRecordStub;
use Podium\Tests\Stubs\GroupActiveRecordStub;
use Podium\Tests\Stubs\MemberActiveRecordStub;
use stdClass;
use yii\base\NotSupportedException;
use yii\data\ActiveDataProvider;

class CategoryRepositoryTest extends TestCase
{
    private CategoryRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new CategoryRepository();
        $this->repository->activeRecordClass = CategoryActiveRecordStub::class;
        CategoryActiveRecordStub::resetStub();
    }

    public function testGetEmptyModel(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('You need to call fetchOne() or setModel() first!');

        $this->repository->getModel();
    }

    public function testSetWrongModel(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('You need to pass Podium\ActiveRecordApi\ActiveRecords\CategoryActiveRecord!');

        $this->repository->setModel(new BookmarkActiveRecordStub());
    }

    public function testGetEmptyCollection(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('You need to call fetchAll() or setCollection() first!');

        $this->repository->getCollection();
    }

    public function testGetCollection(): void
    {
        $this->repository->setCollection(new ActiveDataProvider(['key' => 'key']));
        self::assertSame('key', $this->repository->getCollection()->key);
    }

    public function testGetParent(): void
    {
        $this->expectException(NotSupportedException::class);
        $this->expectExceptionMessage('Category has no parent!');

        $this->repository->getParent();
    }

    public function testFetchOneWithNoModelFound(): void
    {
        self::assertFalse($this->repository->fetchOne(1));
    }

    public function testFetchOneWithModelFound(): void
    {
        CategoryActiveRecordStub::$findResult = new CategoryActiveRecordStub(['id' => 1]);
        self::assertTrue($this->repository->fetchOne(1));
        self::assertSame(1, $this->repository->getModel()->id);
    }

    public function testFetchAllWithInvalidFilter(): void
    {
        $this->expectException(NotSupportedException::class);
        $this->expectExceptionMessage('Only filters implementing yii\data\DataFilter are supported!');

        $this->repository->fetchAll(new stdClass());
    }

    public function testFetchAllWithoutArguments(): void
    {
        $this->repository->fetchAll();
        self::assertInstanceOf(CategoryActiveRecordStub::class, $this->repository->getCollection()->query);
    }

    public function testFetchAllWithFilter(): void
    {
        ActiveDataFilterStub::$buildCalled = false;
        ActiveDataFilterStub::$buildResult = ['a' => 'b'];
        $this->repository->fetchAll(new ActiveDataFilterStub());
        self::assertTrue(ActiveDataFilterStub::$buildCalled);
    }

    public function testFetchAllWithSort(): void
    {
        $this->repository->fetchAll(null, false);
        self::assertFalse($this->repository->getCollection()->sort);
    }

    public function testFetchAllWithPagination(): void
    {
        $this->repository->fetchAll(null, null, false);
        self::assertFalse($this->repository->getCollection()->pagination);
    }

    public function testEditSuccess(): void
    {
        $this->repository->setModel(new CategoryActiveRecordStub());
        self::assertTrue($this->repository->edit());
    }

    public function testEditLoadFail(): void
    {
        CategoryActiveRecordStub::$loadResult = false;
        $this->repository->setModel(new CategoryActiveRecordStub());
        self::assertFalse($this->repository->edit(['name' => 1]));
    }

    public function testEditValidationFail(): void
    {
        CategoryActiveRecordStub::$validationResult = false;
        $this->repository->setModel(new CategoryActiveRecordStub());
        self::assertFalse($this->repository->edit());
        self::assertSame(['attribute' => ['error']], $this->repository->getErrors());
    }

    public function testEditSaveFail(): void
    {
        CategoryActiveRecordStub::$saveResult = false;
        $this->repository->setModel(new CategoryActiveRecordStub());
        self::assertFalse($this->repository->edit());
    }

    public function testHasGroupsMoreRequiredThanExisting(): void
    {
        $this->repository->setModel(new CategoryActiveRecordStub(['groups' => []]));
        self::assertFalse($this->repository->hasGroups([1]));
    }

    public function testHasGroupsSuccess(): void
    {
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('getId')->willReturn(1);

        $this->repository->setModel(new CategoryActiveRecordStub(['groups' => [new GroupActiveRecordStub(['id' => 1])]]));
        self::assertTrue($this->repository->hasGroups([$group]));
    }

    public function testHasGroupsFail(): void
    {
        $group = $this->createMock(GroupRepositoryInterface::class);
        $group->method('getId')->willReturn(1);

        $this->repository->setModel(new CategoryActiveRecordStub(['groups' => [new GroupActiveRecordStub(['id' => 2])]]));
        self::assertFalse($this->repository->hasGroups([$group]));
    }

    public function testJoinGroupSuccess(): void
    {
        $group = new GroupRepository();
        $group->setModel(new GroupActiveRecordStub());

        $this->repository->setModel(new CategoryActiveRecordStub());
        self::assertTrue($this->repository->join($group));
    }

    public function testJoinGroupFail(): void
    {
        $group = new GroupRepository();
        $group->setModel(new GroupActiveRecordStub());

        CategoryActiveRecordStub::$linkResult = false;
        $this->repository->setModel(new CategoryActiveRecordStub());
        self::assertFalse($this->repository->join($group));
    }

    public function testLeaveGroupSuccess(): void
    {
        $group = new GroupRepository();
        $group->setModel(new GroupActiveRecordStub());

        $this->repository->setModel(new CategoryActiveRecordStub());
        self::assertTrue($this->repository->leave($group));
    }

    public function testLeaveGroupFail(): void
    {
        $group = new GroupRepository();
        $group->setModel(new GroupActiveRecordStub());

        CategoryActiveRecordStub::$unlinkResult = false;
        $this->repository->setModel(new CategoryActiveRecordStub());
        self::assertFalse($this->repository->leave($group));
    }

    public function testGetErrors(): void
    {
        self::assertSame([], $this->repository->getErrors());
    }

    public function testDeletePremature(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('You need to call fetchOne() or setModel() first!');

        $this->repository->delete();
    }

    public function testDeleteSuccess(): void
    {
        $this->repository->setModel(new CategoryActiveRecordStub());
        self::assertTrue($this->repository->delete());
    }

    public function testDeleteFail(): void
    {
        CategoryActiveRecordStub::$deleteResult = false;
        $this->repository->setModel(new CategoryActiveRecordStub());
        self::assertFalse($this->repository->delete());
    }

    public function testCreateWithNonIntAuthorId(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Invalid author ID!');

        $this->repository->create($this->createMock(MemberRepositoryInterface::class));
    }

    public function testCreateWithLoadFail(): void
    {
        CategoryActiveRecordStub::$loadResult = false;
        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('getId')->willReturn(1);

        self::assertFalse($this->repository->create($author));
    }

    public function testCreateWithSortAndValidationFail(): void
    {
        CategoryActiveRecordStub::$validationResult = false;
        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('getId')->willReturn(1);

        self::assertFalse($this->repository->create($author, ['sort' => 1]));
        self::assertSame(['attribute' => ['error']], $this->repository->getErrors());
    }

    public function testCreateWithSortAndSaveSuccess(): void
    {
        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('getId')->willReturn(1);

        self::assertTrue($this->repository->create($author, ['sort' => 1]));
        self::assertSame(1, $this->repository->getModel()->sort);
        self::assertSame(1, $this->repository->getModel()->author_id);
    }

    public function testCreateWithoutSortAndExistingCategory(): void
    {
        CategoryActiveRecordStub::$findResult = new CategoryActiveRecordStub(['sort' => 15]);
        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('getId')->willReturn(1);

        self::assertTrue($this->repository->create($author));
        self::assertSame(16, $this->repository->getModel()->sort);
        self::assertSame(1, $this->repository->getModel()->author_id);
    }

    public function testCreateWithoutSortAndNoCategories(): void
    {
        $author = $this->createMock(MemberRepositoryInterface::class);
        $author->method('getId')->willReturn(1);

        self::assertTrue($this->repository->create($author));
        self::assertSame(0, $this->repository->getModel()->sort);
        self::assertSame(1, $this->repository->getModel()->author_id);
    }

    public function testPrematureIsArchived(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('You need to call fetchOne() or setModel() first!');

        $this->repository->isArchived();
    }

    public function testArchiveValidationFail(): void
    {
        CategoryActiveRecordStub::$validationResult = false;
        $this->repository->setModel(new CategoryActiveRecordStub());

        self::assertFalse($this->repository->archive());
        self::assertSame(['attribute' => ['error']], $this->repository->getErrors());
    }

    public function testArchiveSuccess(): void
    {
        $this->repository->setModel(new CategoryActiveRecordStub(['archived' => false]));

        self::assertTrue($this->repository->archive());
        self::assertTrue($this->repository->getModel()->archived);
        self::assertTrue($this->repository->isArchived());
    }

    public function testReviveValidationFail(): void
    {
        CategoryActiveRecordStub::$validationResult = false;
        $this->repository->setModel(new CategoryActiveRecordStub());

        self::assertFalse($this->repository->revive());
        self::assertSame(['attribute' => ['error']], $this->repository->getErrors());
    }

    public function testReviveSuccess(): void
    {
        $this->repository->setModel(new CategoryActiveRecordStub(['archived' => true]));

        self::assertTrue($this->repository->revive());
        self::assertFalse($this->repository->getModel()->archived);
        self::assertFalse($this->repository->isArchived());
    }

    public function testSetOrderValidationFail(): void
    {
        CategoryActiveRecordStub::$validationResult = false;
        $this->repository->setModel(new CategoryActiveRecordStub());

        self::assertFalse($this->repository->setOrder(1));
        self::assertSame(['attribute' => ['error']], $this->repository->getErrors());
    }

    public function testSetOrderSaveFail(): void
    {
        CategoryActiveRecordStub::$saveResult = false;
        $this->repository->setModel(new CategoryActiveRecordStub());

        self::assertFalse($this->repository->setOrder(1));
    }

    public function testSetOrderSuccess(): void
    {
        $this->repository->setModel(new CategoryActiveRecordStub(['sort' => 10]));

        self::assertTrue($this->repository->setOrder(2));
        self::assertSame(2, $this->repository->getModel()->sort);
        self::assertSame(2, $this->repository->getOrder());
    }

    public function testSortEmptySet(): void
    {
        self::assertTrue($this->repository->sort());
    }

    public function testSortFail(): void
    {
        CategoryActiveRecordStub::$saveResult = false;
        CategoryActiveRecordStub::$eachResult = [new CategoryActiveRecordStub()];

        self::assertFalse($this->repository->sort());
    }

    public function testSortSuccess(): void
    {
        $cat1 = new CategoryActiveRecordStub(['sort' => 3]);
        $cat2 = new CategoryActiveRecordStub(['sort' => 56]);
        $cat3 = new CategoryActiveRecordStub(['sort' => 17]);
        CategoryActiveRecordStub::$eachResult = [$cat1, $cat2, $cat3];

        self::assertTrue($this->repository->sort());
        self::assertSame(0, $cat1->sort);
        self::assertSame(1, $cat2->sort);
        self::assertSame(2, $cat3->sort);
    }

    public function testHideValidationFail(): void
    {
        CategoryActiveRecordStub::$validationResult = false;
        $this->repository->setModel(new CategoryActiveRecordStub());

        self::assertFalse($this->repository->hide());
        self::assertSame(['attribute' => ['error']], $this->repository->getErrors());
    }

    public function testHideSuccess(): void
    {
        $this->repository->setModel(new CategoryActiveRecordStub(['visible' => true]));

        self::assertTrue($this->repository->hide());
        self::assertFalse($this->repository->getModel()->visible);
        self::assertTrue($this->repository->isHidden());
    }

    public function testRevealValidationFail(): void
    {
        CategoryActiveRecordStub::$validationResult = false;
        $this->repository->setModel(new CategoryActiveRecordStub());

        self::assertFalse($this->repository->reveal());
        self::assertSame(['attribute' => ['error']], $this->repository->getErrors());
    }

    public function testRevealSuccess(): void
    {
        $this->repository->setModel(new CategoryActiveRecordStub(['visible' => false]));

        self::assertTrue($this->repository->reveal());
        self::assertTrue($this->repository->getModel()->visible);
        self::assertFalse($this->repository->isHidden());
    }

    public function testGetAuthor(): void
    {
        $this->repository->setModel(new CategoryActiveRecordStub(['author' => new MemberActiveRecordStub(['id' => 9])]));

        self::assertSame(9, $this->repository->getAuthor()->getId());
    }
}
