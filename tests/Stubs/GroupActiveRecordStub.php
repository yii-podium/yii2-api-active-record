<?php

declare(strict_types=1);

namespace Podium\Tests\Stubs;

use Podium\ActiveRecordApi\ActiveRecords\GroupActiveRecord;

class GroupActiveRecordStub extends GroupActiveRecord
{
    use ActiveRecordStubTrait;

    public function attributes(): array
    {
        return [
            'id',
            'name',
            'created_at',
            'updated_at',
        ];
    }
}
