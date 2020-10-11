<?php

declare(strict_types=1);

namespace Podium\Tests\Fixtures;

use Podium\ActiveRecordApi\ActiveRecords\AcquaintanceActiveRecord;
use yii\test\ActiveFixture;

class AcquaintanceFixture extends ActiveFixture
{
    public $modelClass = AcquaintanceActiveRecord::class;

    public $depends = [MemberFixture::class];
}
