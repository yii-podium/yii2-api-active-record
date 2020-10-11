<?php

declare(strict_types=1);

namespace Podium\Tests\Fixtures;

use Podium\ActiveRecordApi\ActiveRecords\CategoryActiveRecord;
use yii\test\ActiveFixture;

class CategoryFixture extends ActiveFixture
{
    public $modelClass = CategoryActiveRecord::class;

    public $depends = [MemberFixture::class];
}
