<?php

declare(strict_types=1);

namespace Podium\Tests\Repositories;

use DomainException;
use LogicException;
use PHPUnit\Framework\TestCase;
use Podium\ActiveRecordApi\Enums\AcquaintanceType;
use Podium\ActiveRecordApi\Repositories\AcquaintanceRepository;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Tests\Stubs\AcquaintanceActiveRecordStub;

class AcquaintanceRepositoryTest extends TestCase
{
    private AcquaintanceRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new AcquaintanceRepository();
        AcquaintanceActiveRecordStub::resetStub();
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
            $this->createMock(MemberRepositoryInterface::class)
        );
    }

    public function testFetchOneWithNotIntTargetId(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Invalid target ID!');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);

        $this->repository->fetchOne($member, $this->createMock(MemberRepositoryInterface::class));
    }

    public function testFetchOneWithNoModelFound(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);

        $this->repository->activeRecordClass = AcquaintanceActiveRecordStub::class;
        self::assertFalse($this->repository->fetchOne($member, $target));
    }

    public function testFetchOneWithModelFound(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);

        AcquaintanceActiveRecordStub::$findResult = new AcquaintanceActiveRecordStub(['member_id' => 1]);
        $this->repository->activeRecordClass = AcquaintanceActiveRecordStub::class;
        self::assertTrue($this->repository->fetchOne($member, $target));
        self::assertSame(1, $this->repository->getModel()->member_id);
    }

    public function testPrepareWithNotIntMemberId(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Invalid member ID!');

        $this->repository->prepare(
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
    }

    public function testPrepareWithNotIntTargetId(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Invalid target ID!');

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);

        $this->repository->prepare($member, $this->createMock(MemberRepositoryInterface::class));
    }

    public function testPrepare(): void
    {
        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('getId')->willReturn(1);
        $target = $this->createMock(MemberRepositoryInterface::class);
        $target->method('getId')->willReturn(2);

        $this->repository->activeRecordClass = AcquaintanceActiveRecordStub::class;
        $this->repository->prepare($member, $target);
        $ar = $this->repository->getModel();
        self::assertSame(1, $ar->member_id);
        self::assertSame(2, $ar->target_id);
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
        $this->repository->setModel(new AcquaintanceActiveRecordStub());
        self::assertTrue($this->repository->delete());
    }

    public function testDeleteFail(): void
    {
        AcquaintanceActiveRecordStub::$deleteResult = false;
        $this->repository->setModel(new AcquaintanceActiveRecordStub());
        self::assertFalse($this->repository->delete());
    }

    public function testBefriendValidation(): void
    {
        AcquaintanceActiveRecordStub::$validationResult = false;
        $this->repository->setModel(new AcquaintanceActiveRecordStub());
        self::assertFalse($this->repository->befriend());
        self::assertSame(AcquaintanceType::FRIEND, $this->repository->getModel()->type_id);
        self::assertSame(['attribute' => ['error']], $this->repository->getErrors());
    }

    public function testBefriendSaveFail(): void
    {
        AcquaintanceActiveRecordStub::$saveResult = false;
        $this->repository->setModel(new AcquaintanceActiveRecordStub());
        self::assertFalse($this->repository->befriend());
        self::assertSame(AcquaintanceType::FRIEND, $this->repository->getModel()->type_id);
        self::assertTrue(AcquaintanceActiveRecordStub::$validateCalled);
        self::assertTrue(AcquaintanceActiveRecordStub::$saveCalled);
    }

    public function testBefriendSaveSuccess(): void
    {
        $this->repository->setModel(new AcquaintanceActiveRecordStub());
        self::assertTrue($this->repository->befriend());
        self::assertSame(AcquaintanceType::FRIEND, $this->repository->getModel()->type_id);
        self::assertTrue($this->repository->isFriend());
        self::assertFalse($this->repository->isIgnoring());
    }

    public function testIgnoreValidation(): void
    {
        AcquaintanceActiveRecordStub::$validationResult = false;
        $this->repository->setModel(new AcquaintanceActiveRecordStub());
        self::assertFalse($this->repository->ignore());
        self::assertSame(AcquaintanceType::IGNORE, $this->repository->getModel()->type_id);
        self::assertSame(['attribute' => ['error']], $this->repository->getErrors());
    }

    public function testIgnoreSaveFail(): void
    {
        AcquaintanceActiveRecordStub::$saveResult = false;
        $this->repository->setModel(new AcquaintanceActiveRecordStub());
        self::assertFalse($this->repository->ignore());
        self::assertSame(AcquaintanceType::IGNORE, $this->repository->getModel()->type_id);
        self::assertTrue(AcquaintanceActiveRecordStub::$validateCalled);
        self::assertTrue(AcquaintanceActiveRecordStub::$saveCalled);
    }

    public function testIgnoreSaveSuccess(): void
    {
        $this->repository->setModel(new AcquaintanceActiveRecordStub());
        self::assertTrue($this->repository->ignore());
        self::assertSame(AcquaintanceType::IGNORE, $this->repository->getModel()->type_id);
        self::assertFalse($this->repository->isFriend());
        self::assertTrue($this->repository->isIgnoring());
    }
}
