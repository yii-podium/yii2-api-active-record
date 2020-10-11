<?php

declare(strict_types=1);

namespace Podium\Tests\Member;

use Podium\ActiveRecordApi\ActiveRecords\MemberActiveRecord;
use Podium\ActiveRecordApi\Enums\MemberStatus;
use Podium\ActiveRecordApi\Repositories\MemberRepository;
use Podium\Tests\DbTestCase;
use Podium\Tests\Fixtures\MemberFixture;
use yii\helpers\Json;

use function str_repeat;
use function time;

class MemberBuilderTest extends DbTestCase
{
    public function fixtures(): array
    {
        return [MemberFixture::class];
    }

    public function providerForIds(): array
    {
        return [
            'int' => [100],
            'string' => ['200'],
            'array' => [[1, 2]],
        ];
    }

    /**
     * @dataProvider providerForIds
     *
     * @param int|string|array $id
     */
    public function testRegistering($id): void
    {
        $response = $this->podium->member->register($id, ['username' => 'test member']);

        self::assertTrue($response->getResult());

        $model = MemberActiveRecord::findOne(['user_id' => Json::encode($id)]);
        $repository = new MemberRepository();
        $repository->setModel($model);

        self::assertSame('test member', $model->username);
        self::assertSame('test-member', $model->slug);
        self::assertSame(MemberStatus::ACTIVE, $model->status_id);
        self::assertEqualsWithDelta(time(), $model->created_at, 10);
        self::assertEqualsWithDelta(time(), $model->updated_at, 10);
        self::assertFalse($repository->isBanned());
    }

    public function testRegisteringErrorsWhenUsernameIsSame(): void
    {
        $response = $this->podium->member->register(100, ['username' => 'Member1']);

        self::assertFalse($response->getResult());
        self::assertSame(['username' => ['Username "Member1" has already been taken.']], $response->getErrors());
    }

    public function testRegisteringErrorsWhenSlugIsSame(): void
    {
        $response = $this->podium->member->register(100, ['username' => 'Member New 1', 'slug' => 'Member1']);

        self::assertFalse($response->getResult());
        self::assertSame(['slug' => ['User Slug "Member1" has already been taken.']], $response->getErrors());
    }

    public function testRegisteringErrorsWhenSlugIsTooLong(): void
    {
        $response = $this->podium->member->register(
            100,
            ['username' => 'Member New 1', 'slug' => str_repeat('a', 192)]
        );

        self::assertFalse($response->getResult());
        self::assertSame(['slug' => ['User Slug should contain at most 191 characters.']], $response->getErrors());
    }

    public function testRegisteringErrorsWhenUsernameIsTooLong(): void
    {
        $response = $this->podium->member->register(
            100,
            ['username' => str_repeat('a', 192), 'slug' => 'slug']
        );

        self::assertFalse($response->getResult());
        self::assertSame(['username' => ['Username should contain at most 191 characters.']], $response->getErrors());
    }

    public function testRegisteringErrorsWhenUsernameIsNotGiven(): void
    {
        $response = $this->podium->member->register(100);

        self::assertFalse($response->getResult());
        self::assertSame(['username' => ['Username cannot be blank.']], $response->getErrors());
    }

    public function testEditingWithoutChanges(): void
    {
        $repository = new MemberRepository();
        $repository->setModel(MemberActiveRecord::findOne(1));

        $response = $this->podium->member->edit($repository);

        self::assertTrue($response->getResult());

        $model = MemberActiveRecord::findOne(1);
        self::assertSame('Member1', $model->username);
        self::assertSame('Member1', $model->slug);
        self::assertSame(MemberStatus::ACTIVE, $model->status_id);
        self::assertEquals(1, $model->created_at);
        self::assertEquals(1, $model->updated_at);
        self::assertFalse($repository->isBanned());
    }

    public function testEditingUsername(): void
    {
        $repository = new MemberRepository();
        $repository->setModel(MemberActiveRecord::findOne(1));

        $response = $this->podium->member->edit($repository, ['username' => 'Different Member']);

        self::assertTrue($response->getResult());

        $model = MemberActiveRecord::findOne(1);
        self::assertSame('Different Member', $model->username);
        self::assertSame('Member1', $model->slug);
        self::assertSame(MemberStatus::ACTIVE, $model->status_id);
        self::assertEquals(1, $model->created_at);
        self::assertEqualsWithDelta(time(), $model->updated_at, 10);
        self::assertFalse($repository->isBanned());
    }

    public function testEditingSlug(): void
    {
        $repository = new MemberRepository();
        $repository->setModel(MemberActiveRecord::findOne(1));

        $response = $this->podium->member->edit($repository, ['slug' => 'aaa']);

        self::assertTrue($response->getResult());

        $model = MemberActiveRecord::findOne(1);
        self::assertSame('Member1', $model->username);
        self::assertSame('aaa', $model->slug);
        self::assertSame(MemberStatus::ACTIVE, $model->status_id);
        self::assertEquals(1, $model->created_at);
        self::assertEqualsWithDelta(time(), $model->updated_at, 10);
        self::assertFalse($repository->isBanned());
    }

    public function testEditingErrorsWhenSlugIsSame(): void
    {
        $repository = new MemberRepository();
        $repository->setModel(MemberActiveRecord::findOne(1));

        $response = $this->podium->member->edit($repository, ['slug' => 'Member2']);

        self::assertFalse($response->getResult());
        self::assertSame(['slug' => ['User Slug "Member2" has already been taken.']], $response->getErrors());

        $model = MemberActiveRecord::findOne(1);
        self::assertSame('Member1', $model->username);
        self::assertSame('Member1', $model->slug);
        self::assertSame(MemberStatus::ACTIVE, $model->status_id);
        self::assertEquals(1, $model->created_at);
        self::assertEquals(1, $model->updated_at);
        self::assertFalse($repository->isBanned());
    }

    public function testEditingErrorsWhenUsernameIsSame(): void
    {
        $repository = new MemberRepository();
        $repository->setModel(MemberActiveRecord::findOne(1));

        $response = $this->podium->member->edit($repository, ['username' => 'Member2']);

        self::assertFalse($response->getResult());
        self::assertSame(['username' => ['Username "Member2" has already been taken.']], $response->getErrors());

        $model = MemberActiveRecord::findOne(1);
        self::assertSame('Member1', $model->username);
        self::assertSame('Member1', $model->slug);
        self::assertSame(MemberStatus::ACTIVE, $model->status_id);
        self::assertEquals(1, $model->created_at);
        self::assertEquals(1, $model->updated_at);
        self::assertFalse($repository->isBanned());
    }
}
