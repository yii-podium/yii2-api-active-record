<?php

declare(strict_types=1);

namespace Podium\Tests\Stubs;

use Podium\ActiveRecordApi\ActiveRecords\AcquaintanceActiveRecord;

class AcquaintanceActiveRecordStub extends AcquaintanceActiveRecord
{
    use ActiveRecordStubTrait;

    public function attributes()
    {
        return ['member_id', 'target_id', 'type_id', 'created_at', 'updated_at'];
    }
}
