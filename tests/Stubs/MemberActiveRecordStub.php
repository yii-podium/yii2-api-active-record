<?php

declare(strict_types=1);

namespace Podium\Tests\Stubs;

use Podium\ActiveRecordApi\ActiveRecords\MemberActiveRecord;

class MemberActiveRecordStub extends MemberActiveRecord
{
    use ActiveRecordStubTrait;

    public function attributes(): array
    {
        return ['id', 'user_id', 'username', 'slug', 'status_id', 'created_at', 'updated_at'];
    }
}
