<?php

declare(strict_types=1);

namespace Podium\Tests\Fixtures;

use Podium\ActiveRecordApi\ActiveRecords\CategoryGroupActiveRecord;
use yii\test\ActiveFixture;

class CategoryGroupFixture extends ActiveFixture
{
    public $modelClass = CategoryGroupActiveRecord::class;

    public $depends = [
        GroupFixture::class,
        CategoryFixture::class,
    ];
}
