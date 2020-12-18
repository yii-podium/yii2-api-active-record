<?php

declare(strict_types=1);

namespace Podium\Tests\Fixtures;

use Podium\ActiveRecordApi\ActiveRecords\ThreadGroupActiveRecord;
use yii\test\ActiveFixture;

class ThreadGroupFixture extends ActiveFixture
{
    public $modelClass = ThreadGroupActiveRecord::class;

    public $depends = [
        GroupFixture::class,
        ThreadFixture::class,
    ];
}
