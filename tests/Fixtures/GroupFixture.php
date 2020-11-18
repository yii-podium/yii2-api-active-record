<?php

declare(strict_types=1);

namespace Podium\Tests\Fixtures;

use Podium\ActiveRecordApi\ActiveRecords\GroupActiveRecord;
use yii\test\ActiveFixture;

class GroupFixture extends ActiveFixture
{
    public $modelClass = GroupActiveRecord::class;
}
