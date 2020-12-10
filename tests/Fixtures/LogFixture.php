<?php

declare(strict_types=1);

namespace Podium\Tests\Fixtures;

use Podium\ActiveRecordApi\ActiveRecords\LogActiveRecord;
use yii\test\ActiveFixture;

class LogFixture extends ActiveFixture
{
    public $modelClass = LogActiveRecord::class;

    public $depends = [MemberFixture::class];
}
