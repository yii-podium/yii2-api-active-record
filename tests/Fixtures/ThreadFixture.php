<?php

declare(strict_types=1);

namespace Podium\Tests\Fixtures;

use Podium\ActiveRecordApi\ActiveRecords\ThreadActiveRecord;
use yii\test\ActiveFixture;

class ThreadFixture extends ActiveFixture
{
    public $modelClass = ThreadActiveRecord::class;

    public $depends = [ForumFixture::class];
}
