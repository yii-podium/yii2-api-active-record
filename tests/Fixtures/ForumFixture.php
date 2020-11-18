<?php

declare(strict_types=1);

namespace Podium\Tests\Fixtures;

use Podium\ActiveRecordApi\ActiveRecords\ForumActiveRecord;
use yii\test\ActiveFixture;

class ForumFixture extends ActiveFixture
{
    public $modelClass = ForumActiveRecord::class;

    public $depends = [
        MemberFixture::class,
        CategoryFixture::class,
    ];
}
