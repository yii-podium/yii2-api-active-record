<?php

declare(strict_types=1);

namespace Podium\Tests\Stubs;

use Podium\ActiveRecordApi\ActiveRecords\BookmarkActiveRecord;

class BookmarkActiveRecordStub extends BookmarkActiveRecord
{
    use ActiveRecordStubTrait;

    public function attributes(): array
    {
        return ['member_id', 'thread_id', 'last_seen', 'updated_at'];
    }
}
