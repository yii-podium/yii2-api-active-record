<?php

declare(strict_types=1);

namespace Podium\Tests\Repositories;

use DomainException;
use LogicException;
use PHPUnit\Framework\TestCase;
use Podium\ActiveRecordApi\Repositories\BookmarkRepository;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Tests\Stubs\BookmarkActiveRecordStub;

class BookmarkRepositoryTest extends TestCase
{
    private BookmarkRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new BookmarkRepository();
        BookmarkActiveRecordStub::resetStub();
    }

    public function testGetEmptyModel(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('You need to call fetchOne() or setModel() first!');

        $this->repository->getModel();
    }

    public function testFetchOneWithNotIntMemberId(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Invalid member ID!');

        $this->repository->fetchOne(
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(ThreadRepositoryInterface::class)
        );
    }

    public function testFetchOneWithNotIntThreadId(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Invalid thread ID!');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);

        $this->repository->fetchOne($member, $this->createMock(ThreadRepositoryInterface::class));
    }

    public function testFetchOneWithNoModelFound(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('getId')->willReturn(2);

        $this->repository->activeRecordClass = BookmarkActiveRecordStub::class;
        self::assertFalse($this->repository->fetchOne($member, $thread));
    }

    public function testFetchOneWithModelFound(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('getId')->willReturn(2);

        BookmarkActiveRecordStub::$findResult = new BookmarkActiveRecordStub(['member_id' => 1]);
        $this->repository->activeRecordClass = BookmarkActiveRecordStub::class;
        self::assertTrue($this->repository->fetchOne($member, $thread));
        self::assertSame(1, $this->repository->getModel()->member_id);
    }

    public function testPrepareWithNotIntMemberId(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Invalid member ID!');

        $this->repository->prepare(
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(ThreadRepositoryInterface::class)
        );
    }

    public function testPrepareWithNotIntThreadId(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Invalid thread ID!');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);

        $this->repository->prepare($member, $this->createMock(ThreadRepositoryInterface::class));
    }

    public function testPrepare(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $thread = $this->createMock(ThreadRepositoryInterface::class);
        $thread->method('getId')->willReturn(2);

        $this->repository->activeRecordClass = BookmarkActiveRecordStub::class;
        $this->repository->prepare($member, $thread);
        $ar = $this->repository->getModel();
        self::assertSame(1, $ar->member_id);
        self::assertSame(2, $ar->thread_id);
    }

    public function testGetErrors(): void
    {
        self::assertSame([], $this->repository->getErrors());
    }

    public function testLastSeenPremature(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('You need to call fetchOne() or setModel() first!');

        $this->repository->getLastSeen();
    }

    public function testLastSeenSuccess(): void
    {
        $this->repository->setModel(new BookmarkActiveRecordStub(['last_seen' => 1]));
        self::assertSame(1, $this->repository->getLastSeen());
    }

    public function testMarkValidation(): void
    {
        BookmarkActiveRecordStub::$validationResult = false;
        $this->repository->setModel(new BookmarkActiveRecordStub());
        self::assertFalse($this->repository->mark(10));
        self::assertSame(10, $this->repository->getModel()->last_seen);
        self::assertSame(['attribute' => ['error']], $this->repository->getErrors());
    }

    public function testMarkSaveFail(): void
    {
        BookmarkActiveRecordStub::$saveResult = false;
        $this->repository->setModel(new BookmarkActiveRecordStub());
        self::assertFalse($this->repository->mark(9));
        self::assertSame(9, $this->repository->getModel()->last_seen);
        self::assertTrue(BookmarkActiveRecordStub::$validateCalled);
        self::assertTrue(BookmarkActiveRecordStub::$saveCalled);
    }

    public function testMarkSaveSuccess(): void
    {
        $this->repository->setModel(new BookmarkActiveRecordStub());
        self::assertTrue($this->repository->mark(8));
        self::assertSame(8, $this->repository->getModel()->last_seen);
    }
}
