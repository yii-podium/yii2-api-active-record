<?php

declare(strict_types=1);

namespace Podium\Tests\Fixtures;

use Podium\ActiveRecordApi\ActiveRecords\ForumGroupActiveRecord;
use yii\test\ActiveFixture;

class ForumGroupFixture extends ActiveFixture
{
    public $modelClass = ForumGroupActiveRecord::class;

    public $depends = [
        GroupFixture::class,
        ForumFixture::class,
    ];
}
